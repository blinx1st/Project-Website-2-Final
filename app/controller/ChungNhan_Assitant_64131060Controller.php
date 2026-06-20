<?php
class ChungNhan_Assitant_64131060Controller extends Controller
{
    private string $controllerName = 'ChungNhan_Assitant_64131060';
    private string $listAction = 'ChungNhan_Assitant_64131060';
    private string $pageTitle = 'Chứng nhận (Trợ giảng)';

    public function ChungNhan_Assitant_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listCertificates(null, null, $this->currentMemberId()), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findCertificate((string)$keys['MaChungNhan']), true, fn($row) => $this->guardEventScope((string)$row['MaSuKien']));
    }

    public function Create(): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createCertificate($data), 'Thêm chứng nhận', null, fn($data) => $this->guardEventScope((string)($data['MaSuKien'] ?? '')));
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findCertificate((string)$keys['MaChungNhan']), fn($keys, $data) => $this->repo()->updateCertificate((string)$keys['MaChungNhan'], $data), 'Cập nhật chứng nhận', fn($row) => $this->guardEventScope((string)$row['MaSuKien']), fn($data) => $this->guardEventScope((string)($data['MaSuKien'] ?? '')));
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findCertificate((string)$keys['MaChungNhan']), fn($keys) => $this->repo()->deleteCertificate((string)$keys['MaChungNhan']), true, fn($row) => $this->guardEventScope((string)$row['MaSuKien']));
    }

    public function In(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $cert = $this->repo()->findCertificate(trim($_GET['MaChungNhan'] ?? ($params[0] ?? '')));
        if (!$cert) {
            $this->notFound('Không tìm thấy chứng nhận.');
            return;
        }
        $this->guardEventScope((string)$cert['MaSuKien']);
        $this->render('chungnhan/print', ['title' => 'In chứng nhận', 'cert' => $cert]);
    }

    private function guardEventScope(string $maSuKien): void
    {
        if (!$maSuKien || !$this->repo()->canManageEvent($maSuKien, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function cfg(): array { return $this->resourceCfg('ChungNhan'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
