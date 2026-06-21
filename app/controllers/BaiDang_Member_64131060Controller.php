<?php
class BaiDang_Member_64131060Controller extends Controller
{
    private string $controllerName = 'BaiDang_Member_64131060';
    private string $listAction = 'BaiDang_Member_64131060';
    private string $pageTitle = 'Bài đăng';

    public function BaiDang_Member_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TV']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listPosts(), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findPost((string)$keys['MaBaiDang']), false);
    }

    public function Create(): void { $this->denyUnauthorized(); }
    public function Edit(...$params): void { $this->denyUnauthorized(); }
    public function Delete(...$params): void { $this->denyUnauthorized(); }

    private function cfg(): array { return $this->resourceCfg('BaiDang'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
