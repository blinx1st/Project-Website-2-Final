<?php
class NhomHocTap_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'NhomHocTap_Admin_64131060';
    private string $listAction = 'NhomHocTap_Admin_64131060';
    private string $pageTitle = 'Nhóm học tập';

    public function NhomHocTap_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listStudyGroups(), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroup((string)$keys['MaNhom']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createStudyGroup($data), 'Thêm nhóm học tập');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroup((string)$keys['MaNhom']), fn($keys, $data) => $this->repo()->updateStudyGroup((string)$keys['MaNhom'], $data), 'Cập nhật nhóm học tập');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroup((string)$keys['MaNhom']), fn($keys) => $this->repo()->deleteStudyGroup((string)$keys['MaNhom']));
    }

    private function cfg(): array { return $this->resourceCfg('NhomHocTap'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
