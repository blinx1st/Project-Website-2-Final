<?php
// Trait này gom các hàm CRUD dùng chung để controller con không phải lặp lại code tạo form/list/details/delete.
trait CrudSupport
{
    protected function renderCrudList(string $title, string $controller, string $listAction, array $cfg, array $rows, bool $canWrite, array $extra = []): void
    {
        $this->render('generic/list', $extra + [
            'title' => $title,
            'controller' => $controller,
            'listAction' => $listAction,
            'cfg' => $cfg,
            'rows' => $rows,
            'canWrite' => $canWrite,
        ]);
    }

    protected function renderCrudDetails(string $controller, string $listAction, array $cfg, array $row, array $keys, bool $canWrite, string $title = ''): void
    {
        $this->render('generic/details', [
            'title' => $title ?: 'Thông tin chi tiết ' . lower_text($cfg['title']),
            'controller' => $controller,
            'listAction' => $listAction,
            'cfg' => $cfg,
            'row' => $row,
            'keys' => $keys,
            'canWrite' => $canWrite,
        ]);
    }

    protected function renderCrudForm(string $controller, string $listAction, array $cfg, array $row, string $action, string $title, string $error = '', array $keys = [], bool $canWrite = true, array $relations = []): void
    {
        $this->render('generic/form', [
            'cfg' => $cfg,
            'row' => $row,
            'action' => $action,
            'title' => $title,
            'error' => $error,
            'keys' => $keys,
            'relations' => $relations ?: $this->relationsForCfg($cfg),
            'controller' => $controller,
            'listAction' => $listAction,
            'canWrite' => $canWrite,
        ]);
    }

    protected function renderCrudDelete(string $controller, string $listAction, array $cfg, array $row, array $keys, bool $canWrite, string $error = ''): void
    {
        $this->render('generic/delete', [
            'title' => 'Xóa ' . lower_text($cfg['title']),
            'controller' => $controller,
            'listAction' => $listAction,
            'cfg' => $cfg,
            'row' => $row,
            'keys' => $keys,
            'error' => $error,
            'canWrite' => $canWrite,
        ]);
    }

    protected function relationsForCfg(array $cfg): array
    {
        $relations = [];
        foreach ($cfg['fields'] as $field => $meta) {
            if (($meta['type'] ?? '') === 'select' && isset($meta['relation'])) {
                $relations[$field] = $this->repo()->options($meta['relation']);
            }
        }
        return $relations;
    }

    protected function collectResourceData(array $cfg, array $existing = []): array
    {
        $data = [];
        foreach ($cfg['fields'] as $field => $meta) {
            $type = $meta['type'] ?? 'text';
            if (($meta['readonly'] ?? false) && in_array($field, $cfg['auto'] ?? [], true)) {
                continue;
            }
            if ($type === 'image') {
                $data[$field] = $this->handleUpload($field, $existing[$field] ?? ($_POST[$field] ?? ''));
                continue;
            }
            $value = $_POST[$field] ?? '';
            if (($meta['nullable'] ?? false) && trim((string)$value) === '') {
                $data[$field] = null;
                continue;
            }
            if ($type === 'datetime') {
                $value = $value === '' ? date('Y-m-d H:i:s') : str_replace('T', ' ', $value);
                if (strlen((string)$value) === 16) {
                    $value .= ':00';
                }
            }
            if ($type === 'date' && $value === '') {
                $value = date('Y-m-d');
            }
            $data[$field] = $value;
        }
        return $data;
    }

    protected function keysFromRequest(array $cfg, array $params): array
    {
        $keys = [];
        foreach ($cfg['pk'] as $index => $pk) {
            if (isset($_POST[$pk])) {
                $keys[$pk] = $_POST[$pk];
            } elseif (isset($_GET[$pk])) {
                $keys[$pk] = $_GET[$pk];
            } elseif (isset($params[$index])) {
                $keys[$pk] = $params[$index];
            } elseif (isset($_GET['id']) && count($cfg['pk']) === 1) {
                $keys[$pk] = $_GET['id'];
            }
        }
        return $keys;
    }

    protected function crudDetailsAction(string $controller, string $listAction, array $cfg, array $keys, callable $find, bool $canWrite, ?callable $scope = null, string $missingMessage = 'Không tìm thấy dữ liệu.'): void
    {
        $row = $find($keys);
        if (!$row) {
            $this->notFound($missingMessage);
            return;
        }
        if ($scope) {
            $scope($row);
        }
        $this->renderCrudDetails($controller, $listAction, $cfg, $row, $keys, $canWrite);
    }

    protected function crudCreateAction(string $controller, string $listAction, array $cfg, callable $create, string $title, ?array $redirect = null, ?callable $beforeWrite = null, bool $canWrite = true, array $relations = []): void
    {
        if ($this->isPost()) {
            $row = $this->collectResourceData($cfg);
            try {
                Validator::validateResource($cfg, $row);
                if ($beforeWrite) {
                    $beforeWrite($row);
                }
                $create($row);
                redirect_to($redirect['controller'] ?? $controller, $redirect['action'] ?? $listAction, $redirect['params'] ?? []);
            } catch (Throwable $e) {
                $this->renderCrudForm($controller, $listAction, $cfg, $row, 'Create', $title, $e->getMessage(), [], $canWrite, $relations);
            }
            return;
        }
        $this->renderCrudForm($controller, $listAction, $cfg, [], 'Create', $title, '', [], $canWrite, $relations);
    }

    protected function crudEditAction(string $controller, string $listAction, array $cfg, array $keys, callable $find, callable $update, string $title, ?callable $scope = null, ?callable $beforeWrite = null, bool $canWrite = true, array $relations = []): void
    {
        $row = $find($keys);
        if (!$row) {
            $this->notFound();
            return;
        }
        if ($scope) {
            $scope($row);
        }
        if ($this->isPost()) {
            $data = $this->collectResourceData($cfg, $row);
            try {
                Validator::validateResource($cfg, array_merge($data, $keys));
                if ($beforeWrite) {
                    $beforeWrite($data);
                }
                $update($keys, $data);
                redirect_to($controller, $listAction);
            } catch (Throwable $e) {
                $this->renderCrudForm($controller, $listAction, $cfg, array_merge($row, $data), 'Edit', $title, $e->getMessage(), $keys, $canWrite, $relations);
            }
            return;
        }
        $this->renderCrudForm($controller, $listAction, $cfg, $row, 'Edit', $title, '', $keys, $canWrite, $relations);
    }

    protected function crudDeleteAction(string $controller, string $listAction, array $cfg, array $keys, callable $find, callable $delete, bool $canWrite = true, ?callable $scope = null): void
    {
        $row = $find($keys);
        if (!$row) {
            $this->notFound();
            return;
        }
        if ($scope) {
            $scope($row);
        }
        if ($this->isPost()) {
            try {
                $delete($keys);
                redirect_to($controller, $listAction);
            } catch (Throwable $e) {
                $this->renderCrudDelete($controller, $listAction, $cfg, $row, $keys, $canWrite, 'Không thể xóa vì dữ liệu đang được sử dụng ở bảng khác. ' . $e->getMessage());
            }
            return;
        }
        $this->renderCrudDelete($controller, $listAction, $cfg, $row, $keys, $canWrite);
    }

    private function handleUpload(string $field, string $current = ''): string
    {
        $inputName = $field . '_upload';
        if (isset($_FILES[$inputName]) && is_uploaded_file($_FILES[$inputName]['tmp_name'])) {
            Validator::validateImageUpload($_FILES[$inputName]);
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES[$inputName]['name']));
            $target = PUBLIC_PATH . '/Image/' . $safe;
            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0777, true);
            }
            move_uploaded_file($_FILES[$inputName]['tmp_name'], $target);
            return $safe;
        }
        return $_POST[$field] ?? $current;
    }
}
