<?php
/**
 * Base controller - all controllers extend this
 */
class BaseController
{
    /**
     * Render a view with layout
     * @param string $view - relative path from views/ (e.g. 'admin/dashboard')
     * @param string $layout - layout name (admin, teacher, student, kiosk, auth, default)
     * @param array $data - data passed to the view
     */
    protected function view(string $view, string $layout = 'default', array $data = []): void
    {
        $data['title'] = $data['title'] ?? APP_NAME;
        $data['content_file'] = $view;
        $data['content_data'] = $data;

        // Also make all data available for the view
        extract($data);
        $contentVars = $data;
        $contentScripts = $data['content_scripts'] ?? null;
        $inlineScript = $data['inline_script'] ?? null;

        $layoutFile = BASE_PATH . "/views/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            // Fallback: render without layout
            $viewFile = BASE_PATH . '/views/' . $view . '.php';
            if (file_exists($viewFile)) {
                $title = $data['title'] ?? APP_NAME;
                require $viewFile;
            }
        }
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonError(string $message, int $status = 422): void
    {
        $this->json(['success' => false, 'message' => $message], $status);
    }

    public static function verifyCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = Session::get('csrf_token', '');
        if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
            return false;
        }
        return true;
    }

    protected function csrfField(): string
    {
        $token = Session::get('csrf_token') ?: '';
        if (empty($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set('csrf_token', $token);
        }
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    protected function redirect(string $uri): void
    {
        header('Location: ' . APP_URL . $uri);
        exit;
    }

    protected function redirectFlash(string $uri, string $type, string $message): void
    {
        Session::flash($type, $message);
        $this->redirect($uri);
    }

    protected function getIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    protected function getDevice(): string
    {
        return substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 200);
    }

    protected function getBrowser(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (strpos($ua, 'Edg/') !== false) return 'Edge';
        if (strpos($ua, 'Firefox/') !== false) return 'Firefox';
        if (strpos($ua, 'Chrome/') !== false) return 'Chrome';
        if (strpos($ua, 'Safari/') !== false) return 'Safari';
        return 'Unknown';
    }
}
