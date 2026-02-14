document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (toggleBtn && passwordInput) {
        const eyeOpen = toggleBtn.querySelector('.eye-open');
        const eyeClosed = toggleBtn.querySelector('.eye-closed');
        
        toggleBtn.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeOpen.style.display = isPassword ? 'none' : 'block';
            eyeClosed.style.display = isPassword ? 'block' : 'none';
        });
    }

    // Add subtle focus animation
    const inputs = document.querySelectorAll('.input-wrap input');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.closest('.field').classList.add('focused');
        });
        input.addEventListener('blur', () => {
            input.closest('.field').classList.remove('focused');
        });
    });
});
