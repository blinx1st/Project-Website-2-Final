<?php
class BaiDang_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'BaiDang_Admin_64131060';
    private string $listAction = 'BaiDang_Admin_64131060';
    private string $pageTitle = 'Bài đăng';

    public function BaiDang_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listPosts(), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findPost((string)$keys['MaBaiDang']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createPost($data), 'Thêm bài đăng');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findPost((string)$keys['MaBaiDang']), fn($keys, $data) => $this->repo()->updatePost((string)$keys['MaBaiDang'], $data), 'Cập nhật bài đăng');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findPost((string)$keys['MaBaiDang']), fn($keys) => $this->repo()->deletePost((string)$keys['MaBaiDang']));
    }

    private function cfg(): array { return $this->resourceCfg('BaiDang'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
