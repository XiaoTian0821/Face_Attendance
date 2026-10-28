<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/AuditModel.php';

class StudentPasswordController extends BaseController
{
    public function update(): void
    {
        if (!self::verifyCsrf()) {
            $this->jsonError('Invalid CSRF', 403);
        }

        $userId = (int) Session::get('user_id');
        if (!$userId) {
            $this->jsonError('Not authenticated', 401);
        }

        $currentPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (strlen($newPass) < 8) {
            $this->jsonError('New password must be at least 8 characters.', 422);
        }
        if ($newPass !== $confirmPass) {
            $this->jsonError('New passwords do not match.', 422);
        }

        $user = UserModel::findById($userId);
        if (!password_verify($currentPass, $user['password_hash'])) {
            $this->jsonError('Current password is incorrect.', 422);
        }

        $newHash = password_hash($newPass, PASSWORD_DEFAULT);
        UserModel::resetPassword($userId, $newHash);

        AuditModel::log($userId, null, 'password_changed', $this->getIp(), $this->getDevice(), $this->getBrowser());

        $this->json(['success' => true, 'message' => 'Password updated successfully.']);
    }
}
