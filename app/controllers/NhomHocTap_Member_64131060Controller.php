<?php
class NhomHocTap_Member_64131060Controller extends Controller
{
    private string $controllerName = 'NhomHocTap_Member_64131060';
    private string $listAction = 'NhomHocTap_Member_64131060';
    private string $pageTitle = 'Nhóm học tập của tôi';

    public function NhomHocTap_Member_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TV']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listStudyGroupsForMember($this->currentMemberId()), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TV']);
        $keys = $this->keysFromRequest($this->cfg(), $params);
        $maNhom = (string)($keys['MaNhom'] ?? '');
        $row = $this->repo()->findStudyGroup($maNhom);
        if (!$row) {
            $this->notFound('Không tìm thấy nhóm học tập.');
            return;
        }
        if (!$this->repo()->isStudyGroupMember($maNhom, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
        $this->renderCrudDetails($this->controllerName, $this->listAction, $this->cfg(), $row, ['MaNhom' => $maNhom], false);
    }

    private function cfg(): array { return $this->resourceCfg('NhomHocTap'); }
}
