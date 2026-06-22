<?php
// Controller công khai chỉ cho xem danh sách/chi tiết bài đăng, không cho sửa nội dung.
class BaiDang_64131060Controller extends Controller
{
    // Metadata này cho CrudSupport biết route quay về, tiêu đề trang và resource cần xử lý.
    private string $controllerName = 'BaiDang_64131060';
    private string $listAction = 'BaiDang_64131060';
    private string $pageTitle = 'Bài đăng';

    public function BaiDang_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listPosts(), false);
    }

    public function Details(...$params): void
    {
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findPost((string)$keys['MaBaiDang']), false);
    }

    public function Create(): void { $this->denyUnauthorized(); }
    public function Edit(...$params): void { $this->denyUnauthorized(); }
    public function Delete(...$params): void { $this->denyUnauthorized(); }

    private function cfg(): array { return $this->resourceCfg('BaiDang'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
