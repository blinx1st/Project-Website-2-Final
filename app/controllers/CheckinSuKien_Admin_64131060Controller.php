<?php
class CheckinSuKien_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'CheckinSuKien_Admin_64131060';
    private string $listAction = 'CheckinSuKien_Admin_64131060';
    private string $pageTitle = 'Log check-in sự kiện';

    public function CheckinSuKien_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listCheckins(), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findCheckin($keys['MaCheckin']), false);
    }

    public function Create(): void { $this->denyUnauthorized(); }
    public function Edit(...$params): void { $this->denyUnauthorized(); }
    public function Delete(...$params): void { $this->denyUnauthorized(); }

    private function cfg(): array { return $this->resourceCfg('CheckinSuKien'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
