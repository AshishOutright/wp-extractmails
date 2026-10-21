<?php
/**
 * Component: ExtractMails App Launch Floating Toast Popup (Bottom-Left Corner)
 *
 * @package ExtractMails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Destination URL when user clicks "Open App" or the card
$app_url = 'https://app.extractmails.com/'; // Adjust to your actual app/login URL if needed
$banner_img = 'https://www.extractmails.com/wp-content/uploads/2024/12/extractmails-app-popup.png';

// Don't interrupt sign-up, login or buying flows.
if ( is_page( array( 'pricing', 'login', 'signup', 'sign-up', 'register', 'checkout', 'cart', 'my-account' ) ) ) {
	return;
}
?>

<!-- ExtractMails Bottom-Left Floating Promo Widget (Zero Background Blur) -->
<div id="emAppPopupWidget" class="em-popup-toast" role="complementary" aria-label="<?php esc_attr_e( 'ExtractMails App Launch Announcement', 'extractmails' ); ?>" style="display: none;">
	<!-- Close Button -->
	<button type="button" class="em-popup-close-btn" id="emPopupCloseBtn" aria-label="<?php esc_attr_e( 'Close popup', 'extractmails' ); ?>">
		<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
			<line x1="18" y1="6" x2="6" y2="18"></line>
			<line x1="6" y1="6" x2="18" y2="18"></line>
		</svg>
	</button>

	<!-- Clickable Card Banner -->
	<a href="<?php echo esc_url( $app_url ); ?>" class="em-popup-card-link" id="emPopupCardLink" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Open ExtractMails App', 'extractmails' ); ?>">
		<img 
			src="<?php echo esc_url( $banner_img ); ?>" 
			alt="<?php esc_attr_e( 'The ExtractMails App Is Now Live - Open App', 'extractmails' ); ?>" 
			class="em-popup-card-img"
			width="926" 
			height="1024" 
			loading="eager"
		/>
	</a>
</div>

<style>
/* Floating Toast Container (Bottom-Left Corner - No Screen Blur) */
.em-popup-toast {
	position: fixed !important;
	bottom: 28px !important;
	left: 28px !important;
	width: 330px !important;
	max-width: calc(100vw - 44px) !important;
	z-index: 9999999 !important;
	border-radius: 28px !important;
	box-shadow: 
		0 20px 50px -10px rgba(0, 0, 0, 0.45), 
		0 0 35px rgba(29, 114, 250, 0.35) !important;
	transform: translateY(35px) scale(0.95);
	opacity: 0;
	visibility: hidden;
	transition: 
		transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), 
		opacity 0.35s ease, 
		visibility 0.35s ease,
		box-shadow 0.3s ease !important;
	overflow: visible !important;
	background: transparent !important;
}

.em-popup-toast.is-visible {
	transform: translateY(0) scale(1) !important;
	opacity: 1 !important;
	visibility: visible !important;
}

.em-popup-toast:hover {
	box-shadow: 
		0 25px 60px -8px rgba(0, 0, 0, 0.55), 
		0 0 45px rgba(29, 114, 250, 0.5) !important;
}

/* Close Button (Top-Right corner of the card) */
.em-popup-close-btn {
	position: absolute !important;
	top: 14px !important;
	right: 14px !important;
	width: 32px !important;
	height: 32px !important;
	border-radius: 50% !important;
	background: rgba(255, 255, 255, 0.95) !important;
	color: #0d1b2a !important;
	border: 1.5px solid rgba(0, 102, 255, 0.3) !important;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
	display: flex !important;
	align-items: center !important;
	justify-content: center !important;
	cursor: pointer !important;
	z-index: 20 !important;
	padding: 0 !important;
	margin: 0 !important;
	outline: none !important;
	transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.em-popup-close-btn:hover {
	background: #0066ff !important;
	color: #ffffff !important;
	border-color: #0066ff !important;
	transform: rotate(90deg) scale(1.12) !important;
	box-shadow: 0 4px 16px rgba(0, 102, 255, 0.5) !important;
}

/* Card Link & Image */
.em-popup-card-link {
	display: block !important;
	width: 100% !important;
	line-height: 0 !important;
	text-decoration: none !important;
	border-radius: 26px !important;
	overflow: hidden !important;
	cursor: pointer !important;
}

.em-popup-card-img {
	display: block !important;
	width: 100% !important;
	height: auto !important;
	border-radius: 26px !important;
	transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
	object-fit: cover !important;
}

.em-popup-card-link:hover .em-popup-card-img {
	transform: scale(1.018) !important;
}

/* Mobile Responsive */
@media (max-width: 640px) {
	.em-popup-toast {
		bottom: 16px !important;
		left: 16px !important;
		width: 290px !important;
		border-radius: 22px !important;
	}

	.em-popup-card-link,
	.em-popup-card-img {
		border-radius: 22px !important;
	}

	.em-popup-close-btn {
		top: 10px !important;
		right: 10px !important;
		width: 28px !important;
		height: 28px !important;
	}

	.em-popup-close-btn svg {
		width: 13px !important;
		height: 13px !important;
	}
}

.em-popup-close-btn:focus-visible {
	outline: 3px solid #6CB5FF !important;
	outline-offset: 2px !important;
}

@media (prefers-reduced-motion: reduce) {
	.em-popup-toast,
	.em-popup-close-btn,
	.em-popup-card-img {
		transition: opacity 0.2s ease !important;
		transform: none !important;
	}
}
</style>

<script>
(function() {
	/*
	 * When the popup appears:
	 *   - Closed (X / Esc) or "Open App" clicked  -> never shown again in this browser, on any page.
	 *   - Ignored                                -> at most once per visit (first page only), on the
	 *                                               next visits again, up to MAX_VISITS visits in total.
	 *   - Appears after DELAY_MS or after scrolling SCROLL_SHARE of the page, whichever comes first.
	 * Change CAMPAIGN for a new promotion: everyone sees the new popup once, even if they closed this one.
	 * ?preview_popup=1 always shows it and does not record anything (for testing).
	 */
	const CAMPAIGN     = 'app-launch-2026';
	const MAX_VISITS   = 3;
	const DELAY_MS     = 5000;
	const SCROLL_SHARE = 0.3;

	const STATE_KEY   = 'em_popup_' + CAMPAIGN;          // remembered across visits
	const SESSION_KEY = 'em_popup_' + CAMPAIGN + '_seen'; // remembered for this visit only

	// Storage with a cookie fallback (some private/locked-down browsers block localStorage).
	function readCookie(name) {
		const m = document.cookie.match('(?:^|; )' + name + '=([^;]*)');
		return m ? decodeURIComponent(m[1]) : null;
	}
	function writeCookie(name, value, days) {
		let c = name + '=' + encodeURIComponent(value) + '; path=/; SameSite=Lax';
		if (days) c += '; max-age=' + (days * 86400);
		if (location.protocol === 'https:') c += '; Secure';
		document.cookie = c;
	}
	function getState() {
		let raw = null;
		try { raw = window.localStorage.getItem(STATE_KEY); } catch (e) {}
		if (raw === null) raw = readCookie(STATE_KEY);
		try { return JSON.parse(raw) || {}; } catch (e) { return {}; }
	}
	function setState(state) {
		const raw = JSON.stringify(state);
		try { window.localStorage.setItem(STATE_KEY, raw); } catch (e) {}
		writeCookie(STATE_KEY, raw, 365);
	}
	function seenThisVisit() {
		try { if (window.sessionStorage.getItem(SESSION_KEY)) return true; } catch (e) {}
		return readCookie(SESSION_KEY) === '1';
	}
	function markSeenThisVisit() {
		try { window.sessionStorage.setItem(SESSION_KEY, '1'); } catch (e) {}
		writeCookie(SESSION_KEY, '1'); // session cookie: cleared when the browser is closed
	}

	function initEmAppPopup() {
		const toast = document.getElementById('emAppPopupWidget');
		const closeBtn = document.getElementById('emPopupCloseBtn');
		const cardLink = document.getElementById('emPopupCardLink');

		if (!toast || !closeBtn) return;

		const isPreview = new URLSearchParams(window.location.search).get('preview_popup') === '1';
		const state = getState();

		if (!isPreview) {
			if (state.dismissed) return;                         // closed or opened before
			if ((state.visits || 0) >= MAX_VISITS) return;       // shown enough times
			if (seenThisVisit()) return;                         // already shown on this visit
		}

		let isTriggered = false;
		let timer = 0;

		function showToast() {
			if (isTriggered) return;
			isTriggered = true;
			clearTimeout(timer);
			window.removeEventListener('scroll', onScrollCheck);

			if (!isPreview) {
				markSeenThisVisit();
				setState({ visits: (state.visits || 0) + 1, last: Date.now() });
			}

			toast.style.display = 'block';
			void toast.offsetHeight; // Force reflow
			toast.classList.add('is-visible');
			document.addEventListener('keydown', onKey);
		}

		function hideToast() {
			toast.classList.remove('is-visible');
			document.removeEventListener('keydown', onKey);
			setTimeout(function() {
				toast.style.display = 'none';
			}, 380);
		}

		// Closed or used: remember it so it never comes back in this browser.
		function dismiss() {
			if (!isPreview) {
				setState({ dismissed: true, visits: (getState().visits || 1), at: Date.now() });
			}
			hideToast();
		}

		function onKey(e) {
			if (e.key === 'Escape') dismiss();
		}

		closeBtn.addEventListener('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			dismiss();
		});

		// "Open App" opens in a new tab; the visitor has seen the offer, so stop showing it.
		if (cardLink) {
			cardLink.addEventListener('click', function() {
				dismiss();
			});
		}

		if (isPreview) {
			setTimeout(showToast, 300);
			return;
		}

		function onScrollCheck() {
			const scrollPos = window.scrollY || window.pageYOffset;
			const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
			if (totalHeight > 0 && (scrollPos / totalHeight) >= SCROLL_SHARE) {
				showToast();
			}
		}

		timer = setTimeout(showToast, DELAY_MS);
		window.addEventListener('scroll', onScrollCheck, { passive: true });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initEmAppPopup);
	} else {
		initEmAppPopup();
	}
})();
</script>
