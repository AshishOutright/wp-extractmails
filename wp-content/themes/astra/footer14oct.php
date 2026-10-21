<?php
/**
 * Footer template for ExtractMails SaaS Theme (astra_7 child theme)
 */
?>
<style>
/* ==========================================================================
   ExtractMails Reference Footer Design (Matches ExtractMails-Website-Updated)
   ========================================================================== */
.reference-footer {
  position: relative;
  width: 100%;
  padding: 48px 24px 64px;
  background: transparent;
  color: #132B50;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  box-sizing: border-box;
}

.reference-footer-panel {
  max-width: 1360px;
  margin: 0 auto;
  padding: 44px 48px 36px;
  border: 1px solid #D5EAFD;
  border-radius: 28px;
  background: #ffffff;
  box-shadow: 0 12px 40px rgba(5, 40, 155, 0.08);
  box-sizing: border-box;
}

.reference-footer-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  margin-bottom: 34px;
}

.reference-footer-logo-link {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
  transition: transform 0.2s ease;
}

.reference-footer-logo-link:hover {
  transform: translateY(-1px);
}

.reference-footer-logo {
  display: block;
  width: auto;
  height: 84px;
  max-width: 100%;
  object-fit: contain;
  margin: -14px 0;
}

.reference-social {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  align-items: center;
}

.reference-social a {
  font-size: 14px;
  font-weight: 500;
  color: #0048C2;
  text-decoration: none;
  transition: color 0.15s ease;
}

.reference-social a:hover {
  color: #05289B;
  text-decoration: underline;
}

.reference-footer-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1.35fr 1fr 1fr;
  gap: 24px;
}

.reference-footer-column {
  min-width: 0;
}

.reference-footer-column h2 {
  margin: 0 0 16px;
  padding: 12px 10px;
  font-size: 15px;
  line-height: 1.4;
  letter-spacing: 0;
  font-weight: 600;
  text-align: center;
  color: #ffffff;
  border-radius: 8px;
  background: linear-gradient(105deg, #05289B, #0048C2);
  box-shadow: 0 2px 6px rgba(5, 40, 155, 0.15);
}

.reference-footer-items {
  display: flex;
  flex-direction: column;
  gap: 14px;
  padding: 0 8px;
}

.reference-footer-items a,
.reference-footer-items span {
  font-size: 14px;
  line-height: 1.6;
  color: #132B50;
  text-decoration: none;
  display: block;
  transition: color 0.15s ease;
}

.reference-footer-items a:hover {
  color: #0048C2;
  text-decoration: underline;
}

.reference-footer-items .footer-unlinked {
  color: #132B50;
  cursor: default;
}

/* Company column button-styled links */
.reference-footer-column:last-child .reference-footer-items {
  padding: 0;
  gap: 12px;
}

.reference-footer-column:last-child .reference-footer-items a {
  text-align: center;
  padding: 11px 14px;
  border: 1px solid #D5EAFD;
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  color: #132B50;
  background: #ffffff;
  display: block;
  transition: all 0.2s ease;
}

.reference-footer-column:last-child .reference-footer-items a:hover {
  border-color: #0048C2;
  background: #F6FAFD;
  color: #0048C2;
  text-decoration: none;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 72, 194, 0.08);
}

.reference-footer-bottom {
  margin-top: 36px;
  padding-top: 26px;
  border-top: 1px solid #D5EAFD;
  text-align: center;
}

.reference-footer-bottom p {
  max-width: 820px;
  margin: 0 auto;
  color: #52647F;
  font-size: 13px;
  line-height: 1.7;
}

.reference-footer-legal {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 24px;
  margin-top: 20px;
  font-size: 13px;
  color: #52647F;
}

.reference-footer-legal a {
  color: #52647F;
  text-decoration: none;
  transition: color 0.15s ease;
}

.reference-footer-legal a:hover {
  color: #0048C2;
  text-decoration: underline;
}

@media (max-width: 1100px) {
  .reference-footer-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
  .reference-footer-panel {
    padding: 32px 28px;
  }
}

@media (max-width: 700px) {
  .reference-footer {
    padding: 24px 16px 40px;
  }
  .reference-footer-panel {
    padding: 24px 20px;
    border-radius: 20px;
  }
  .reference-footer-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 24px 16px;
  }
  .reference-footer-top {
    align-items: flex-start;
  }
  .reference-footer-logo {
    height: 64px;
    margin: -10px 0;
  }
  .reference-social {
    justify-content: flex-end;
    gap: 10px 16px;
    max-width: 220px;
  }
  .reference-footer-column:last-child {
    grid-column: 1 / -1;
  }
  .reference-footer-column:last-child .reference-footer-items {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
  }
  .reference-footer-items {
    padding: 0 4px;
  }
}

@media (max-width: 480px) {
  .reference-footer-grid {
    grid-template-columns: 1fr;
  }
  .reference-footer-column:last-child .reference-footer-items {
    grid-template-columns: 1fr;
  }
  .reference-footer-legal {
    gap: 12px 18px;
  }
}
</style>

<footer class="reference-footer" aria-label="Website footer">
  <div class="reference-footer-panel">
    <div class="reference-footer-top">
      <a href="https://www.extractmails.com/" class="reference-footer-logo-link" aria-label="ExtractMails home">
        <img src="https://www.extractmails.com/wp-content/uploads/2024/12/extractmails-footer-logo.png" alt="ExtractMails" class="reference-footer-logo">
      </a>
      <div class="reference-social" aria-label="Social profiles">
        <a href="https://www.facebook.com/profile.php?id=61577511276950" target="_blank" rel="noopener noreferrer">Facebook</a>
        <a href="https://x.com/MailsExtract" target="_blank" rel="noopener noreferrer">X</a>
        <a href="https://www.linkedin.com/company/112438965/admin/page-posts/published/" target="_blank" rel="noopener noreferrer">LinkedIn</a>
        <a href="https://api.whatsapp.com/send?phone=917275894867" target="_blank" rel="noopener noreferrer">WhatsApp</a>
      </div>
    </div>

    <div class="reference-footer-grid">
      <!-- Free tools -->
      <section class="reference-footer-column">
        <h2>Free tools</h2>
        <div class="reference-footer-items">
          <a href="https://www.extractmails.com/tools/reverse-email-lookup/" target="_blank">Reverse Email Lookup</a>
          <a href="https://www.extractmails.com/tools/gmail-generator/" target="_blank">Gmail Generator</a>
          <a href="https://www.extractmails.com/tools/email-spam-checker/" target="_blank">Email Spam Checker</a>
          <a href="https://www.extractmails.com/tools/ai-email-writer/" target="_blank">AI Email Writer</a>
        </div>
      </section>

      <!-- Compare -->
      <section class="reference-footer-column">
        <h2>Compare</h2>
        <div class="reference-footer-items">
          <span class="footer-unlinked" title="Destination not linked yet">vs Hunter</span>
          <span class="footer-unlinked" title="Destination not linked yet">vs Apollo</span>
          <span class="footer-unlinked" title="Destination not linked yet">vs Snov</span>
          <span class="footer-unlinked" title="Destination not linked yet">vs Zoominfo</span>
          <span class="footer-unlinked" title="Destination not linked yet">vs RocketReach</span>
        </div>
      </section>

      <!-- Useful Resources -->
      <section class="reference-footer-column">
        <h2>Useful Resources</h2>
        <div class="reference-footer-items">
          <a href="https://www.extractmails.com/blog/how-to-use-a-google-maps-scraper-for-lead-generation/" target="_blank">Google Maps Scraper for Lead Generation</a>
          <a href="https://www.extractmails.com/blog/how-to-recover-comcast-email-password/" target="_blank">Recover Comcast Email Passwords</a>
          <a href="https://www.extractmails.com/blog/how-to-check-icloud-email/" target="_blank">Check iCloud Email in a Few Easy Steps</a>
          <a href="https://www.extractmails.com/blog/how-to-improve-email-deliverability/" target="_blank">Improve Email Deliverability for Better Open Rates</a>
          <a href="https://www.extractmails.com/blog/how-to-extract-emails-from-linkedin/" target="_blank">Extract Emails from LinkedIn Without Coding</a>
        </div>
      </section>

      <!-- Services -->
      <section class="reference-footer-column">
        <h2>Services</h2>
        <div class="reference-footer-items">
          <a href="https://www.extractmails.com/email-scraper/" target="_blank">Email Scraper</a>
          <a href="https://www.extractmails.com/bulk-email-finder/" target="_blank">Bulk Email Finder</a>
          <a href="https://www.extractmails.com/website-email-extraction-services/" target="_blank">Expert Email Extraction</a>
          <a href="https://www.extractmails.com/email-extractor/" target="_blank">Email Extractor</a>
          <a href="https://www.extractmails.com/lead-generation-services/" target="_blank">Lead Generation</a>
        </div>
      </section>

      <!-- Company -->
      <section class="reference-footer-column">
        <h2>Company</h2>
        <div class="reference-footer-items">
          <a href="https://www.extractmails.com/about-us-page/" target="_blank">About Us</a>
          <a href="https://www.extractmails.com/contact-page/" target="_blank">Contact Us</a>
          <a href="https://www.extractmails.com/blog/" target="_blank">Blogs</a>
        </div>
      </section>
    </div>

    <!-- Divider & Bottom Info -->
    <div class="reference-footer-bottom">
      <p>
        <strong>Copyright &copy; 2026 ExtractMails&trade;.</strong> All Rights Reserved. The <strong>OutRight&reg; logo is a trademark of OutRight Systems Pvt. Ltd.</strong> All content and materials on this site are protected by copyright.
      </p>
      <div class="reference-footer-legal" aria-label="Legal information">
        <a href="https://www.extractmails.com/gdpr-page/" target="_blank">GDPR Legal</a>
        <a href="https://www.extractmails.com/cookie-policy/" target="_blank">Cookie Policy</a>
        <a href="https://www.extractmails.com/privacy-and-policy/" target="_blank">Privacy policy</a>
        <a href="https://www.extractmails.com/terms-and-conditions/" target="_blank">Terms</a>
      </div>
    </div>
  </div>
</footer>
