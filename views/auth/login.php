<div class="auth-form">
    <form method="POST" action="<?php echo APP_URL; ?>/login">
        <?php echo $csrf; ?>
        <div class="mb-3">
            <label for="identifier" class="form-label"><i class="fa-solid fa-user me-2"></i>Username or Email</label>
            <input type="text" class="form-control" name="identifier" id="identifier"
                   placeholder="Enter your username or email" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label"><i class="fa-solid fa-lock me-2"></i>Password</label>
            <div class="input-group">
                <input type="password" class="form-control" name="password" id="password"
                       placeholder="••••••••" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePass" title="Show/Hide">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="fa-solid fa-right-to-bracket me-2"></i>Sign In
        </button>
    </form>
    <hr class="my-3">
    <div class="text-center">
        <a href="<?php echo APP_URL; ?>/privacy" class="small text-decoration-none">
            <i class="fa-solid fa-shield-halved me-1"></i>Privacy Policy
        </a>
    </div>
</div>
<script>
document.getElementById('togglePass').addEventListener('click', function() {
    const pwd = document.getElementById('password');
    const icon = this.querySelector('i');
    if (pwd.type === 'password') { pwd.type = 'text'; icon.className = 'fa-solid fa-eye-slash'; }
    else { pwd.type = 'password'; icon.className = 'fa-solid fa-eye'; }
});
</script>
