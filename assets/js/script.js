jQuery(document).ready(function($) {

    const overlay = $("#ppp-popup-overlay");
    const container = $("#ppp-popup-container");
    const closeButton = $("#ppp-close-button");
    const formSection = $(".ppp-popup-form-section");
    const cookieName = "ppp_popup_viewed";

    // --- Check if popup should be shown ---
    if (getCookie(cookieName)) {
        return; // Don't show if cookie exists
    }

    // --- Show popup after delay ---
    setTimeout(function() {
        showPopup();
    }, 10000); // 10 seconds delay

    // --- Function to show the popup ---
    function showPopup() {
        renderStep1();
        // Add class if no image URL is provided
        if (!ppp_ajax_object.settings.image_url) {
            container.addClass("ppp-no-image");
        } else {
             container.removeClass("ppp-no-image");
        }
        overlay.addClass("ppp-visible");
    }

    // --- Function to hide the popup ---
    function hidePopup() {
        overlay.removeClass("ppp-visible");
    }

    // --- Function to set a cookie ---
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    // --- Function to get a cookie ---
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(";");
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == " ") c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // --- Render Step 1 HTML ---
    function renderStep1() {
        const step1Html = `
            <div class="ppp-step1">
                <h2>${ppp_ajax_object.settings.title || "VOCÊ GANHOU UM DESCONTO!"}</h2>
                <p>${ppp_ajax_object.settings.description || "Basta digitar seu nome e e-mail abaixo:"}</p>
                <form id="ppp-promo-form">
                    <div class="ppp-form-field">
                        <input type="text" id="ppp-name" name="name" required placeholder="Nome">
                    </div>
                    <div class="ppp-form-field">
                        <input type="email" id="ppp-email" name="email" required placeholder="Email">
                    </div>
                    <button type="submit" id="ppp-submit-button">EXIBIR CUPOM</button>
                    <p class="ppp-error-message" style="display: none;"></p>
                </form>
            </div>
        `;
        formSection.html(step1Html);
    }

    // --- Render Step 2 HTML ---
    function renderStep2() {
    const couponText = ppp_ajax_object.settings.coupon_text || "CUPOM ESPECIAL";
    // Extrair apenas o código sem o prefixo "CUPOM:"
    const cleanCouponCode = couponText.includes(":") ? couponText.split(":")[1].trim() : couponText;
    
    const step2Html = `
        <div class="ppp-step2">
            <h2>CUPOM LIBERADO!</h2>
            <p>Copie o código abaixo para usá-lo na finalização da compra e ganhar 5% de desconto.</p>
            <div class="ppp-coupon-wrapper">
                <div class="ppp-coupon-code">${couponText}</div>
                    <button id="ppp-copy-button" type="button" class="ppp-copy-icon" aria-label="Copiar código">
                        <svg id="copy-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M10 1H2a1 1 0 0 0-1 1v11h1V2h8V1z"/>
                            <path d="M14 3H6a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM6 15V4h8v11H6z"/>
                        </svg>
                    </button>
                </div>
            <div id="ppp-copy-feedback" class="ppp-celebration" style="display: none;">
                <span>✓</span>
            </div>
        </div>
    `;
    formSection.html(step2Html);
    
    // Adicionar o evento de clique ao botão de copiar
    $("#ppp-copy-button").on("click", function() {
        copiarParaAreaDeTransferencia(cleanCouponCode);
    });
    }
    
    // --- Função para copiar o texto para a área de transferência ---
    function copiarParaAreaDeTransferencia(texto) {
        // Usar a API moderna de clipboard quando disponível
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(texto).then(function() {
                mostrarFeedbackCopia();
            });
        } else {
            // Fallback para método anterior
            const elementoTemp = $("<textarea>");
            $("body").append(elementoTemp);
            elementoTemp.val(texto).select();
            document.execCommand("copy");
            elementoTemp.remove();
            mostrarFeedbackCopia();
        }
    }
    
    // --- Função para mostrar o feedback de cópia ---
    function mostrarFeedbackCopia() {
        const copyButton = $("#ppp-copy-button");
        const copyIcon = $("#copy-icon");
    
        // Substituir o ícone por um check
        copyIcon.replaceWith(`
            <svg id="copy-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#28a745" viewBox="0 0 16 16">
                <path d="M13.485 1.929a.75.75 0 0 1 1.06 1.06l-8.25 8.25a.75.75 0 0 1-1.06 0L1.454 7.56a.75.75 0 1 1 1.06-1.06L6 10.007l7.485-7.486z"/>
            </svg>
        `);
    
        // Voltar ao ícone de copiar depois de 2s
        setTimeout(function () {
            $("#copy-icon").replaceWith(`
                <svg id="copy-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M10 1H2a1 1 0 0 0-1 1v11h1V2h8V1z"/>
                    <path d="M14 3H6a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM6 15V4h8v11H6z"/>
                </svg>
            `);
        }, 2000);
    }

    // --- Event Listener for Form Submission ---
    // Use event delegation since the form is added dynamically
    formSection.on("submit", "#ppp-promo-form", function(e) {
        e.preventDefault();

        const nameInput = $("#ppp-name");
        const emailInput = $("#ppp-email");
        const errorMsg = formSection.find(".ppp-error-message");
        const submitButton = $("#ppp-submit-button");

        const name = nameInput.val().trim();
        const email = emailInput.val().trim();

        // Basic Frontend Validation
        errorMsg.hide().text("");
        let isValid = true;
        if (!name) {
            errorMsg.text("Por favor, preencha o seu nome.").show();
            nameInput.focus();
            isValid = false;
        }
        if (!email || !/^[^"]+@[^"]+\.[a-zA-Z]{2,}$/.test(email)) { // Simple email regex
             if (isValid) { // Only focus if name was valid
                 emailInput.focus();
             }
            errorMsg.text(errorMsg.text() + (errorMsg.text() ? " " : "") + "Por favor, insira um e-mail válido.").show();
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        // Add loading state
        container.addClass("ppp-loading");
        submitButton.prop("disabled", true);

        // AJAX Request
        $.ajax({
            url: ppp_ajax_object.ajax_url,
            type: "POST",
            data: {
                action: "ppp_submit_form", // Matches PHP action hook
                nonce: ppp_ajax_object.nonce,
                name: name,
                email: email
            },
            success: function(response) {
                if (response.success) {
                    // Set cookie to prevent showing again (e.g., for 30 days)
                    setCookie(cookieName, "1", 30);
                    // Show Step 2
                    renderStep2();
                } else {
                    // Show error message from backend (if provided)
                    errorMsg.text(response.data.message || "Ocorreu um erro. Tente novamente.").show();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorMsg.text("Erro de comunicação. Verifique sua conexão e tente novamente.").show();
            },
            complete: function() {
                // Remove loading state
                container.removeClass("ppp-loading");
                // No need to re-enable button as the form is replaced
            }
        });
    });

    // --- Event Listener for Close Button ---
    closeButton.on("click", function() {
        hidePopup();
        // Set cookie even if closed without submitting
        setCookie(cookieName, "1", 30);
    });

    // --- Event Listener for Overlay Click (optional: close on overlay click) ---
    overlay.on("click", function(e) {
        // Check if the click is directly on the overlay, not the container
        if (e.target === this) {
            hidePopup();
            setCookie(cookieName, "1", 30);
        }
    });

});

/* MAUTIC - VERSÃO DINÂMICA PARA POPUP */
function integrarPopupMautic() {
    // Verificar se a integração com Mautic está ativada
    if (!ppp_ajax_object.mautic || !ppp_ajax_object.mautic.enabled) {
        console.log('🔴 Integração com Mautic desativada nas configurações');
        return;
    }
    
    // Obter configurações do Mautic
    const mauticUrl = ppp_ajax_object.mautic.url || '';
    const mauticFormId = ppp_ajax_object.mautic.form_id || '';
    const mauticFormName = ppp_ajax_object.mautic.form_name || '';

    // Verificar se todas as configurações necessárias estão presentes
    if (!mauticUrl || !mauticFormId || !mauticFormName) {
        console.log('🔴 Configuração incompleta do Mautic. Verifique URL, Form ID e Form Name nas configurações.');
        return;
    }
    
    console.log('🟢 Integração com Mautic ativada:', mauticUrl);
    
    let emailsEnviados = new Set(); // Controlar emails já enviados
    
    // Função para obter o device ID do Mautic
    function obterMauticDeviceId() {
        const cookies = document.cookie.split(';');
        let mauticId = null;
        
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'mtc_id') {
                mauticId = value;
                break;
            }
        }
        
        if (!mauticId && typeof mt !== 'undefined' && mt.getContactId) {
            mauticId = mt.getContactId();
        }
        
        if (!mauticId) {
            mauticId = localStorage.getItem('mautic_device_id') || 
                      sessionStorage.getItem('mautic_device_id');
        }
        
        console.log('🔍 Device ID encontrado:', mauticId);
        return mauticId;
    }
    
    function enviarParaMautic(email) {
        if (emailsEnviados.has(email)) {
            console.log(`📧 Email ${email} já foi enviado para o Mautic`);
            return;
        }
        
        console.log('🚀 Iniciando envio para Mautic:', email);
        
        // PASSO 1: Identificar usuário no tracking ANTES do envio
        if (typeof mt !== 'undefined') {
            mt('send', 'identify', {
                email: email
            });
            console.log('🔗 Usuário identificado no tracking do Mautic');
        }
        
        // PASSO 2: Aguardar um pouco e enviar via formulário
        setTimeout(() => {
            const formData = new FormData();
            const deviceId = obterMauticDeviceId();
            
            formData.append('mauticform[email]', email);
            formData.append('mauticform[formId]', mauticFormId);
            formData.append('mauticform[return]', '');
            formData.append('mauticform[formName]', mauticFormName);
            
            // IMPORTANTE: Adicionar device ID para associação
            if (deviceId) {
                formData.append('mauticform[mautic_device_id]', deviceId);
                console.log('📱 Device ID incluído:', deviceId);
            }
            
            formData.append('mauticform[timestamp]', Date.now());

            fetch(`${mauticUrl}/form/submit?formId=${mauticFormId}`, {
                method: 'POST',
                body: formData,
                mode: 'no-cors',
                credentials: 'include'
            })
            .then(() => {
                console.log('✅ Email enviado com sucesso para Mautic:', email);
                emailsEnviados.add(email);
                
                // PASSO 3: Segunda identificação para garantir associação
                setTimeout(() => {
                    if (typeof mt !== 'undefined') {
                        mt('send', 'identify', {
                            email: email
                        });
                        console.log('🔄 Segunda identificação concluída');
                    }
                }, 2000);
            })
            .catch(error => {
                console.error('❌ Erro ao enviar para Mautic:', error);
            });
        }, 500);
    }

    function validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    // Função para configurar o campo quando ele aparecer
    function configurarCampoEmail() {
        // Usar jQuery já que está disponível no contexto
        const $emailInput = jQuery('#ppp-email');
        
        if ($emailInput.length && !$emailInput.data('mautic-configured')) {
            console.log('🎯 Campo de email do popup encontrado e configurado');
            
            // Marcar como configurado
            $emailInput.data('mautic-configured', true);
            
            // Evento no blur (quando sair do campo)
            $emailInput.on('blur', function() {
                const email = jQuery(this).val().trim();
                
                if (email && validarEmail(email)) {
                    console.log('📧 Email válido capturado do popup:', email);
                    enviarParaMautic(email);
                }
            });
            
            // Evento adicional no change para garantir
            $emailInput.on('change', function() {
                const email = jQuery(this).val().trim();
                
                if (email && validarEmail(email)) {
                    console.log('📧 Email capturado via change:', email);
                    enviarParaMautic(email);
                }
            });
            
            return true;
        }
        
        return false;
    }

    // Tentar configurar imediatamente
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function($) {
            console.log('🔧 Mautic: Aguardando popup aparecer...');
            configurarCampoEmail();
        });
    }

    // Observar quando o popup for renderizado (Step 1)
    function observarPopup() {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) {
                            // Verificar se o campo email foi adicionado
                            if (node.querySelector && node.querySelector('#ppp-email')) {
                                console.log('🔍 Campo email detectado via MutationObserver');
                                setTimeout(configurarCampoEmail, 100);
                            }
                            // Verificar se foi adicionado dentro do node
                            if (node.id === 'ppp-email') {
                                console.log('🔍 Campo email direto detectado');
                                setTimeout(configurarCampoEmail, 100);
                            }
                        }
                    });
                }
            });
        });

        // Observar mudanças na section do formulário
        const formSection = document.querySelector('.ppp-popup-form-section');
        if (formSection) {
            console.log('👀 Observando mudanças na seção do formulário');
            observer.observe(formSection, {
                childList: true,
                subtree: true
            });
        }

        // Observar mudanças em todo o documento (fallback)
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Iniciar observação
    if (typeof MutationObserver !== 'undefined') {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', observarPopup);
        } else {
            observarPopup();
        }
    }
}

// Iniciar integração com Mautic quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', integrarPopupMautic);
