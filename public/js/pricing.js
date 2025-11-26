document.addEventListener('DOMContentLoaded', function() {
    
    const configElement = document.getElementById('pricing-config');
    if (!configElement) return; 

    const config = {
        csrfToken: configElement.dataset.csrf,
        subscribeRoute: configElement.dataset.routeSubscribe,
        checkStatusRoute: configElement.dataset.routeCheck,
        redirectAfterSuccess: configElement.dataset.redirectUrl
    };

    const subscribeButtons = document.querySelectorAll('.subscribe-btn');
    const modalElement = document.getElementById('paypalModal');
    const modal = new bootstrap.Modal(modalElement);
    
    const states = {
        loading: document.getElementById('paypalLoading'),
        processing: document.getElementById('paypalProcessing'),
        success: document.getElementById('paypalSuccess'),
        error: document.getElementById('paypalError'),
        cancelled: document.getElementById('paypalCancelled')
    };
    
    const errorMessage = document.getElementById('paypalErrorMessage');

    let paypalWindow = null;
    let pollInterval = null;
    let windowCheckInterval = null;

    subscribeButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const plan = this.dataset.plan;
            const originalHTML = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cargando...';

            try {
                modal.show();
                showState('loading');

                const response = await fetch(config.subscribeRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ plan: plan })
                });

                const data = await response.json();

                if (data.success && data.approval_url) {
                    openPaypalWindow(data.approval_url);
                    
                    setTimeout(() => {
                        showState('processing');
                        startPolling();
                        checkWindowClosed();
                    }, 1000);

                } else {
                    throw new Error(data.message || 'Error al iniciar el proceso de pago');
                }

            } catch (error) {
                console.error('Error:', error);
                showState('error');
                errorMessage.textContent = error.message;
            } finally {
                this.disabled = false;
                this.innerHTML = originalHTML;
            }
        });
    });

    function openPaypalWindow(url) {
        const width = 600;
        const height = 700;
        const left = (window.screen.width / 2) - (width / 2);
        const top = (window.screen.height / 2) - (height / 2);

        paypalWindow = window.open(
            url,
            'PayPal',
            `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );

        if (!paypalWindow) {
            throw new Error('No se pudo abrir la ventana de PayPal. Por favor, habilita las ventanas emergentes.');
        }
    }

    function showState(stateKey) {
        Object.values(states).forEach(el => el.style.display = 'none');
        
        if (states[stateKey]) {
            states[stateKey].style.display = 'block';
        }
    }

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);

        pollInterval = setInterval(async function() {
            try {
                const response = await fetch(config.checkStatusRoute, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });

                const data = await response.json();

                if (data.subscribed) {
                    handleSuccess();
                }
            } catch (error) {
                console.error('Poll error:', error);
            }
        }, 3000);
    }

    function checkWindowClosed() {
        if (windowCheckInterval) clearInterval(windowCheckInterval);

        windowCheckInterval = setInterval(function() {
            if (paypalWindow && paypalWindow.closed) {
                clearInterval(windowCheckInterval);

                setTimeout(async () => {
                    try {
                        const response = await fetch(config.checkStatusRoute, {
                            method: 'GET',
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();

                        if (data.subscribed) {
                            handleSuccess();
                        } else {
                            stopPolling();
                            showState('cancelled');
                        }
                    } catch (error) {
                        stopPolling();
                        showState('cancelled');
                    }
                }, 1000);
            }
        }, 500);
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
        if (windowCheckInterval) {
            clearInterval(windowCheckInterval);
            windowCheckInterval = null;
        }
    }

    function handleSuccess() {
        stopPolling();
        if (paypalWindow && !paypalWindow.closed) {
            paypalWindow.close();
        }
        showState('success');
        setTimeout(() => {
            modal.hide();
            window.location.href = config.redirectAfterSuccess || window.location.href;
        }, 2000);
    }

    modalElement.addEventListener('hidden.bs.modal', function() {
        stopPolling();
        if (paypalWindow && !paypalWindow.closed) {
            paypalWindow.close();
        }
        // Resetear a estado inicial por si se vuelve a abrir
        setTimeout(() => showState('loading'), 500);
    });
});