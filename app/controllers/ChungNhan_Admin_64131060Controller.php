<?php
class ChungNhan_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'ChungNhan_Admin_64131060';
    private string $listAction = 'ChungNhan_Admin_64131060';
    private string $pageTitle = 'Chứng nhận';

    public function ChungNhan_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listCertificates(), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findCertificate((string)$keys['MaChungNhan']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createCertificate($data), 'Thêm chứng nhận');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findCertificate((string)$keys['MaChungNhan']), fn($keys, $data) => $this->repo()->updateCertificate((string)$keys['MaChungNhan'], $data), 'Cập nhật chứng nhận');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findCertificate((string)$keys['MaChungNhan']), fn($keys) => $this->repo()->deleteCertificate((string)$keys['MaChungNhan']));
    }

    public function In(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->printCertificate(trim($_GET['MaChungNhan'] ?? ($params[0] ?? '')));
    }

    private function printCertificate(string $maChungNhan): void
    {
        $cert = $this->repo()->findCertificate($maChungNhan);
        if (!$cert) {
            $this->notFound('Không tìm thấy chứng nhận.');
            return;
        }
        $this->render('chungnhan/print', ['title' => 'In chứng nhận', 'cert' => $cert]);
    }

    private function cfg(): array { return $this->resourceCfg('ChungNhan'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
