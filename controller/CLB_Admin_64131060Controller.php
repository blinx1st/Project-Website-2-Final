<?php
class CLB_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'CLB_Admin_64131060';
    private string $listAction = 'CLB_Admin_64131060';
    private string $pageTitle = 'Câu lạc bộ';

    public function CLB_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listClubs(), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClub((string)$keys['MaCLB']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createClub($data), 'Thêm câu lạc bộ');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClub((string)$keys['MaCLB']), fn($keys, $data) => $this->repo()->updateClub((string)$keys['MaCLB'], $data), 'Cập nhật câu lạc bộ');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClub((string)$keys['MaCLB']), fn($keys) => $this->repo()->deleteClub((string)$keys['MaCLB']));
    }

    private function cfg(): array { return $this->resourceCfg('CLB'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
