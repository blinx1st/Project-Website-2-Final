<?php
class QuyTacDiemRenLuyen_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'QuyTacDiemRenLuyen_Admin_64131060';
    private string $listAction = 'QuyTacDiemRenLuyen_Admin_64131060';
    private string $pageTitle = 'Quy tắc điểm rèn luyện';

    public function QuyTacDiemRenLuyen_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listTrainingRules(), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findTrainingRule($keys['MaQuyTac']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), function ($data): void {
            $this->repo()->createTrainingRule($data);
            $this->repo()->syncTrainingPointsFromRules();
        }, 'Thêm quy tắc điểm rèn luyện');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findTrainingRule($keys['MaQuyTac']), function ($keys, $data): void {
            $this->repo()->updateTrainingRule($keys['MaQuyTac'], $data);
            $this->repo()->syncTrainingPointsFromRules();
        }, 'Cập nhật quy tắc điểm rèn luyện');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findTrainingRule($keys['MaQuyTac']), function ($keys): void {
            $this->repo()->deleteTrainingRule($keys['MaQuyTac']);
            $this->repo()->syncTrainingPointsFromRules();
        });
    }

    private function cfg(): array { return $this->resourceCfg('QuyTacDiemRenLuyen'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
