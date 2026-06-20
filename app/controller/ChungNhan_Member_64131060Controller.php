<?php
class ChungNhan_Member_64131060Controller extends Controller
{
    private string $controllerName = 'ChungNhan_Member_64131060';
    private string $listAction = 'ChungNhan_Member_64131060';
    private string $pageTitle = 'Chứng nhận của tôi';

    public function ChungNhan_Member_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TV']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listCertificates(null, $this->currentMemberId()), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findCertificate((string)$keys['MaChungNhan']), false, fn($row) => $this->guardOwn((string)$row['MaThanhVien']));
    }

    public function In(...$params): void
    {
        $this->requireRoles(['TV']);
        $cert = $this->repo()->findCertificate(trim($_GET['MaChungNhan'] ?? ($params[0] ?? '')));
        if (!$cert) {
            $this->notFound('Không tìm thấy chứng nhận.');
            return;
        }
        $this->guardOwn((string)$cert['MaThanhVien']);
        $this->render('chungnhan/print', ['title' => 'In chứng nhận', 'cert' => $cert]);
    }

    public function Create(): void { $this->denyUnauthorized(); }
    public function Edit(...$params): void { $this->denyUnauthorized(); }
    public function Delete(...$params): void { $this->denyUnauthorized(); }

    private function guardOwn(string $maThanhVien): void
    {
        if ($maThanhVien !== $this->currentMemberId()) {
            $this->denyUnauthorized();
        }
    }

    private function cfg(): array { return $this->resourceCfg('ChungNhan'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
