<?php
/*
Template Name: AI Email Writer  */

if (! defined('ABSPATH')) {
    exit;
}

if (! function_exists('extractmail_ai_writer_get_azure_api_key')) {
    function extractmail_ai_writer_get_azure_api_key()
    {
        if (defined('EXTRACTMAIL_AZURE_OPENAI_API_KEY') && EXTRACTMAIL_AZURE_OPENAI_API_KEY) {
            return EXTRACTMAIL_AZURE_OPENAI_API_KEY;
        }

        if (defined('AZURE_OPENAI_API_KEY') && AZURE_OPENAI_API_KEY) {
            return AZURE_OPENAI_API_KEY;
        }

        $env_key = getenv('EXTRACTMAIL_AZURE_OPENAI_API_KEY');
        if ($env_key) {
            return $env_key;
        }

        $env_key = getenv('AZURE_OPENAI_API_KEY');
        if ($env_key) {
            return $env_key;
        }

        return '';
    }
}

if (! function_exists('extractmail_ai_writer_get_azure_endpoint')) {
    function extractmail_ai_writer_get_azure_endpoint()
    {
        if (defined('EXTRACTMAIL_AZURE_OPENAI_ENDPOINT') && EXTRACTMAIL_AZURE_OPENAI_ENDPOINT) {
            return EXTRACTMAIL_AZURE_OPENAI_ENDPOINT;
        }

        if (defined('AZURE_OPENAI_ENDPOINT') && AZURE_OPENAI_ENDPOINT) {
            return AZURE_OPENAI_ENDPOINT;
        }

        $env_endpoint = getenv('EXTRACTMAIL_AZURE_OPENAI_ENDPOINT');
        if ($env_endpoint) {
            return $env_endpoint;
        }

        $env_endpoint = getenv('AZURE_OPENAI_ENDPOINT');
        if ($env_endpoint) {
            return $env_endpoint;
        }

        return 'https://ashis-mcud14tl-eastus2.cognitiveservices.azure.com/openai/deployments/gpt-5.2/chat/completions?api-version=2025-04-01-preview';
    }
}

if (! function_exists('extractmail_ai_writer_get_azure_label')) {
    function extractmail_ai_writer_get_azure_label()
    {
        if (defined('EXTRACTMAIL_AZURE_OPENAI_LABEL') && EXTRACTMAIL_AZURE_OPENAI_LABEL) {
            return EXTRACTMAIL_AZURE_OPENAI_LABEL;
        }

        if (defined('AZURE_OPENAI_LABEL') && AZURE_OPENAI_LABEL) {
            return AZURE_OPENAI_LABEL;
        }

        $env_label = getenv('EXTRACTMAIL_AZURE_OPENAI_LABEL');
        if ($env_label) {
            return $env_label;
        }

        $env_label = getenv('AZURE_OPENAI_LABEL');
        if ($env_label) {
            return $env_label;
        }

        $endpoint = extractmail_ai_writer_get_azure_endpoint();
        if (preg_match('~/deployments/([^/]+)/chat/completions~', $endpoint, $matches)) {
            return $matches[1];
        }

        return 'azure-openai';
    }
}

if (! function_exists('extractmail_ai_writer_extract_chat_content')) {
    function extractmail_ai_writer_extract_chat_content($payload)
    {
        if (empty($payload['choices'][0]['message']['content'])) {
            return '';
        }

        $content = $payload['choices'][0]['message']['content'];

        if (is_string($content)) {
            return trim($content);
        }

        if (is_array($content)) {
            $chunks = array();

            foreach ($content as $item) {
                if (isset($item['text']) && is_string($item['text'])) {
                    $chunks[] = trim($item['text']);
                }
            }

            return trim(implode("\n\n", array_filter($chunks)));
        }

        return '';
    }
}

if (! function_exists('extractmail_ai_writer_is_ajax_request')) {
    function extractmail_ai_writer_is_ajax_request()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' === strtolower(wp_unslash($_SERVER['HTTP_X_REQUESTED_WITH']));
    }
}

if (! function_exists('extractmail_ai_writer_call_azure_openai')) {
    function extractmail_ai_writer_call_azure_openai($brief)
    {
        $api_key  = extractmail_ai_writer_get_azure_api_key();
        $endpoint = extractmail_ai_writer_get_azure_endpoint();
        $label    = extractmail_ai_writer_get_azure_label();

        if ('' === $api_key) {
            return new WP_Error('azure_openai_missing_key', 'Azure OpenAI API key is missing. Add EXTRACTMAIL_AZURE_OPENAI_API_KEY to wp-config.php or the server environment.');
        }

        $system_prompt = "You are an expert professional email writer with years of experience crafting clear, persuasive, and context-appropriate emails.

Your task is to write one polished, ready-to-send email based on the user's brief.

OUTPUT FORMAT:
- Start with: Subject: [compelling subject line]
- Follow with a blank line
- Then the complete email body (greeting → body → closing → signature placeholder)

WRITING GUIDELINES:
- Match tone to context: formal for business/cold outreach, warm for follow-ups, direct for internal comms
- Be concise — no filler phrases like 'I hope this email finds you well' unless tone demands it
- Use natural, human-sounding language — never robotic or overly corporate
- Include a clear call to action when appropriate
- Fill in reasonable placeholders (e.g. [Your Name], [Company]) only when info is missing

STRICT RULES:
- Output ONLY the final email — no commentary, explanations, or options
- Do not add bullet points outside the email body
- Do not summarize what you did or ask follow-up questions";
        $request_body = array(
            'messages' => array(
                array(
                    'role'    => 'system',
                    'content' => $system_prompt,
                ),
                array(
                    'role'    => 'user',
                    'content' => $brief,
                ),
            ),
            'max_completion_tokens' => 16384,
            'temperature'           => 0.2,
        );

        $response = wp_remote_post(
            $endpoint,
            array(
                'timeout'   => 45,
                'sslverify' => true,
                'headers'   => array(
                    'api-key'      => $api_key,
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ),
                'body'      => wp_json_encode($request_body),
            )
        );

        if (is_wp_error($response)) {
            return $response;
        }

        $status = (int) wp_remote_retrieve_response_code($response);
        $body   = json_decode((string) wp_remote_retrieve_body($response), true);

        if (200 !== $status) {
            $error_message = is_array($body) && ! empty($body['error']['message']) ? $body['error']['message'] : 'Azure OpenAI returned an unexpected HTTP status.';
            return new WP_Error('azure_openai_http_error', $error_message);
        }

        if (! is_array($body)) {
            return new WP_Error('azure_openai_invalid_response', 'Azure OpenAI returned an invalid response.');
        }

        $output_text = extractmail_ai_writer_extract_chat_content($body);

        if ('' === $output_text) {
            return new WP_Error('azure_openai_empty_output', 'Azure OpenAI did not return any email text for this request.');
        }

        return array(
            'model' => $label,
            'text'  => $output_text,
        );
    }
}

$extractmail_ai_writer_brief  = '';
$extractmail_ai_writer_result = null;
$extractmail_ai_writer_error  = '';

if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['extractmail_ai_writer_submit'])) {
    $extractmail_ai_writer_brief = sanitize_textarea_field(wp_unslash($_POST['email_brief'] ?? ''));

    if (! isset($_POST['extractmail_ai_writer_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['extractmail_ai_writer_nonce'])), 'extractmail_ai_writer')) {
        $extractmail_ai_writer_error = 'Security validation failed. Please refresh the page and try again.';
    } elseif ('' === trim($extractmail_ai_writer_brief)) {
        $extractmail_ai_writer_error = 'Please describe the email you want to write.';
    } else {
        $extractmail_ai_writer_response = extractmail_ai_writer_call_azure_openai($extractmail_ai_writer_brief);

        if (is_wp_error($extractmail_ai_writer_response)) {
            $extractmail_ai_writer_error = $extractmail_ai_writer_response->get_error_message();
        } else {
            $extractmail_ai_writer_result = $extractmail_ai_writer_response;
        }
    }

    if (extractmail_ai_writer_is_ajax_request()) {
        if ('' !== $extractmail_ai_writer_error) {
            wp_send_json_error(
                array(
                    'message' => $extractmail_ai_writer_error,
                )
            );
        }

        wp_send_json_success(
            array(
                'result' => $extractmail_ai_writer_result,
            )
        );
    }

    $extractmail_ai_writer_redirect_path = '/';
    if (! empty($_SERVER['REQUEST_URI'])) {
        $extractmail_ai_writer_redirect_path = (string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH);
    }

    wp_safe_redirect(home_url($extractmail_ai_writer_redirect_path));
    exit;
}

add_action(
    'wp_head',
    static function () {
        ?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap');

  @keyframes extractmail-spin {
    to {
      transform: rotate(360deg);
    }
  }

  .extractmail-ai-spinner {
    width: 20px;
    height: 20px;
    border: 3px solid rgba(37, 99, 235, 0.18);
    border-top-color: #2563eb;
    border-radius: 999px;
    animation: extractmail-spin 0.9s linear infinite;
  }

  .extractmail-ai-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 24px;
  }

  @media (max-width: 1024px) {
    .extractmail-ai-grid {
      grid-template-columns: 1fr;
    }
  }

  #extractmail-ai-writer-section {
    scroll-margin-top: 110px;
  }

  .extractmail-try-now-link,
  .extractmail-try-now-link:hover,
  .extractmail-try-now-link:focus,
  .extractmail-try-now-link:active {
    background: linear-gradient(to right, #1d4ed8, #4ade80) !important;
    border-color: transparent !important;
    color: #ffffff !important;
    opacity: 1 !important;
    outline: none;
    text-decoration: none !important;
  }
</style>
<?php
    },
    1
);


get_header();
?>

<section class="py-12 md:py-10">
  <div class="max-w-[1500px] mx-auto px-4 md:px-6">

    <!-- HERO -->
    <div class="grid md:grid-cols-2 gap-8 md:gap-4 items-center">

      <!-- LEFT -->
      <div>

        <!-- Badge -->

        <span class="inline-block  py-1 text-[14px] md:text-[18px] lg:text-[22px] text-[#8eb7f3] rounded-full mt-12 lg:mt-4 md:mb-8
  ">

          <a href="/" class="hover:underline">Home</a> /
          <a href="/tools" class="hover:underline">Tools</a> /
          <a href="/tools/reverse-email-lookup" class="hover:underline">AI Email Writer Tool  </a>

        </span>


        <!-- Heading -->
        <h1 class="text-[28px] sm:text-[34px] md:text-[42px] lg:text-[49px] leading-snug md:leading-tight font-semibold 
        bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
        bg-clip-text text-transparent mb-4 md:mb-6">
          AI Email Writer Tool 
        </h1>

        <!-- Description -->
        <p
          class="text-gray-600 text-[14px] sm:text-[16px] md:text-[18px] lg:text-[20px] leading-relaxed max-w-xl md:mb-8">
          Instantly generate professional emails with the AI Email Writer, which helps you create, refine, and
          personalize for multiple purposes. 
        </p>

        <button type="button" class="extractmail-try-now-link inline-block px-6 md:px-12 py-1 text-[14px] md:text-[18px] lg:text-[22px] text-white rounded-full mt-12 lg:mt-4 md:mb-8
          bg-gradient-to-r from-blue-700 to-green-400">
          Try Now
        </button>

      </div>

      <!-- RIGHT IMAGE -->
      <div class="flex justify-center md:justify-end mt-6 md:mt-6">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-80-4.png" alt="Hero"
          class="w-full max-w-[300px] sm:max-w-[400px] md:max-w-[480px] lg:max-w-[520px] object-contain">
      </div>

    </div>

    <!-- SECOND SECTION -->


  </div>
</section>



<main id="extractmail-ai-writer-section" style=" background:#ffffff;color:#12295a;font-family:'Manrope',ui-sans-serif,system-ui;">
  <section style="max-width:1280px;margin:0 auto;">

    <div
      style="max-width:1120px;margin:48px auto 0;border:1px solid #dbe6f3;border-radius:18px;background:#ffffff;padding:28px;box-shadow:0 1px 2px rgba(15,23,42,0.04);">
      <div class="extractmail-ai-grid">
        <div>
          <label for="extractmail-ai-brief"
            style="display:block;font-size:18px;font-weight:700;line-height:1.3;color:#12295a;">
            Tell us about your email
          </label>
          <form id="extractmail-ai-writer-form" method="post" autocomplete="off"
            style="position:relative;margin-top:14px;">
            <?php wp_nonce_field('extractmail_ai_writer', 'extractmail_ai_writer_nonce'); ?>
            <input type="hidden" name="extractmail_ai_writer_submit" value="1" />
            <textarea id="extractmail-ai-brief" name="email_brief" rows="11"
              placeholder="Write an email to a friend asking for their opinion on a recent movie or book"
              style="display:block;width:100%;min-height:500px;padding:18px 20px 64px;border:1px solid #9ec5ff;border-radius:10px;background:#ffffff;color:#12295a;font-size:18px;line-height:1.45;box-shadow:0 0 0 3px rgba(96, 165, 250, 0.25) inset;outline:none;resize:none;"
              required><?php echo esc_textarea($extractmail_ai_writer_brief); ?></textarea>
            <button id="extractmail-ai-writer-submit" type="submit" aria-label="Generate email"
              style="position:absolute;right:14px;bottom:14px;display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border:0;border-radius:999px;background:#7aa7f7;color:#ffffff;cursor:pointer;">
              <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none" stroke="currentColor"
                stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"></path>
                <path d="M13 5l7 7-7 7"></path>
              </svg>
            </button>
          </form>
          <div id="extractmail-ai-writer-error"
            style="<?php echo '' !== $extractmail_ai_writer_error ? 'display:block;' : 'display:none;'; ?> margin-top:16px;border:1px solid #fecdd3;border-radius:12px;background:#fff1f2;padding:12px 16px;color:#be123c;font-size:14px;font-weight:600;line-height:1.5;">
            <?php echo esc_html($extractmail_ai_writer_error); ?>
          </div>
        </div>

        <div>
          <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
            <label for="extractmail-ai-result"
              style="display:block;font-size:18px;font-weight:700;line-height:1.3;color:#12295a;">
              Email result
            </label>
          </div>

          <div id="extractmail-ai-loading"
            style="display:none;margin-top:14px;min-height:500px;border:1px solid #cbd5e1;border-radius:10px;background:#f8fafc;padding:20px;">
            <div style="display:flex;align-items:center;gap:12px;">
              <div class="extractmail-ai-spinner" aria-hidden="true"></div>
              <div>
                <p style="margin:0;font-size:17px;font-weight:700;color:#12295a;">Writing your email...</p>
                <p style="margin:4px 0 0;font-size:14px;line-height:1.5;color:#64748b;">Generating a subject line and a
                  polished draft with your Azure OpenAI deployment.</p>
              </div>
            </div>
          </div>

          <div style="position:relative;margin-top:14px;">
            <button id="extractmail-ai-copy" type="button"
              style="<?php echo $extractmail_ai_writer_result ? 'display:inline-flex;' : 'display:none;'; ?> position:absolute;top:14px;right:14px;align-items:center;justify-content:center;width:28px;height:28px;border:0;border-radius:0;background:transparent;color:#64748b;box-shadow:none;cursor:pointer;z-index:2;padding:0;"
              aria-label="Copy generated email">
              <span class="extractmail-ai-copy-default" aria-hidden="true"
                style="display:inline-flex;align-items:center;justify-content:center;">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <rect x="9" y="9" width="11" height="11" rx="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </span>
              <span class="extractmail-ai-copy-success" aria-hidden="true"
                style="display:none;align-items:center;justify-content:center;color:#2563eb;">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2.4"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M5 12.5l4.2 4.2L19 7.4"></path>
                </svg>
              </span>
            </button>
            <div id="extractmail-ai-result"
              style="height:500px;overflow-y:auto;border:1px solid #cbd5e1;border-radius:10px;background:#f8fafc;padding:20px 44px 20px 20px;font-size:18px;line-height:1.85;color:#334155;white-space:pre-wrap;">
              <?php echo $extractmail_ai_writer_result ? esc_html($extractmail_ai_writer_result['text']) : 'Your email content will appear here.'; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<section class="bg-[#f4f4f4] py-20 px-4">
  <div class="max-w-[1200px] mx-auto text-center">

    <!-- Main Heading -->
    <h2 class="text-[28px] md:text-[38px] font-medium text-gray-900 leading-snug mb-6">
      Create High-Quality, Clear, and Professional Email Instantly and Effortlessly 
    </h2>

    <!-- Sub Text -->
    <p class="text-gray-600 text-[14px] md:text-[17px] leading-relaxed mb-14">
      Instantly create polished and accurate professional emails with the AI Email Writer tool by ExtractMails. From
      drafting outreach mail to formal replies, the tool helps you generate engaging, clear, and context-aware emails
      within seconds. 
    </p>

    <!-- Section Heading -->
    <h2 class="text-[24px] md:text-[37px] font-medium text-gray-900 mb-5">
      What is AI Email Writer 
    </h2>

    <!-- Section Text -->
    <p class="text-gray-600 text-[14px] md:text-[17px] leading-relaxed">
      AI Email Writer is a specialized tool that uses advanced artificial intelligence to generate email content, which
      is based on your input. The tool generates an email by understanding the intent, tone, and context of the sender.
      The tool can create well-structured emails for multiple purposes, such as business communication, marketing,
      or personal message. 
    </p>

  </div>
</section>


<section class=" py-10">
  <div class="max-w-[1400px] mx-auto px-6 text-center">

    <!-- Heading -->
    <h2
      class="text-[36px] font-semibold  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent mb-4 leading-snug">
      Key Features of AI Email Writer
    </h2>

    <p class="text-gray-600 text-[14px] md:text-[15px] leading-relaxed mb-10">
      The AI Email Writer tool of ExtractMails is designed to offer faster, smarter, and more effective email
      communication, without sacrificing the quality or personalization of the email. From business proposals to quick
      follow-ups, the AI Email Writer tool ensures to provide you with a clear, impactful, and professional email every
      time.  
    </p>

    <!-- Grid -->
    <div class="grid md:grid-cols-4">

      <!-- ITEM 1 -->
      <div class="px-6 text-center relative">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/instant-messaging-1.png" alt="Instant Email Generation"
          class="w-20 mx-auto mb-6">

        <h3 class="font-bold text-[18px] text-gray-900 mb-3">
          Instant Email Generation
        </h3>

        <p class="text-gray-600 text-[15px] leading-relaxed">
          The tool helps create a complete, ready-to-sent email instantly. It generates a well-structured
          message, eliminating writing from scratch. 
        </p>

        <!-- Divider -->
        <div class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 h-[90%] w-px bg-gray-300"></div>
      </div>

      <!-- ITEM 2 -->
      <div class="px-6 text-center relative">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/creative-3.png" alt="Smart Reply Suggestion" class="w-20 mx-auto mb-6">

        <h3 class="font-bold text-[18px] text-gray-900 mb-3">
          Smart Reply Suggestion
        </h3>

        <p class="text-gray-600 text-[15px] leading-relaxed">
          The tool also features a Smart Reply Suggestion feature, which provides AI-generated replies, making
          responding to an email faster.  
        </p>

        <!-- Divider -->
        <div class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 h-[90%] w-px bg-gray-300"></div>
      </div>

      <!-- ITEM 3 -->
      <div class="px-6 text-center relative">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/letter-1-1.png" alt="Context-Aware Writing" class="w-20 mx-auto mb-6">

        <h3 class="font-bold text-[18px] text-gray-900 mb-3">
          Context-Aware Writing
        </h3>

        <p class="text-gray-600 text-[15px] leading-relaxed">
          The AI Email Writer tool understands the purpose behind the input and crafts an email that matches the
          context. The tools offer tailored-made content to fit the context naturally.  
        </p>

        <!-- Divider -->
        <div class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 h-[90%] w-px bg-gray-300"></div>
      </div>

      <!-- ITEM 4 -->
      <div class="px-6 text-center">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/switch-1.png" alt="Customizable Tone" class="w-20 mx-auto mb-6">

        <h3 class="font-bold text-[18px] text-gray-900 mb-3">
          Customizable Tone
        </h3>

        <p class="text-gray-600 text-[15px] leading-relaxed">
          The user can customize the tone of the email to suit the audience. It ensures that the message resonates with
          the tone of communication. 
        </p>
      </div>

    </div>

  </div>
</section>


<section class="relative overflow-hidden max-w-[1500px] mx-auto my-10 py-16 rounded-[4]">
  <!-- Background Gradient -->
  <!-- Background Gradient -->


  <!-- Rectangles -->

  <div class="absolute w-[687px] h-[272px] left-[24px] top-[425px] bg-[#b1c3ff] blur-[147px]"></div>



  <div class="absolute w-[372px] h-[175px] left-[225px] top-[237px] bg-[#b1c3ff] blur-[147px]"></div>


  <div
    class="absolute w-[715.56px] h-[161.15px] left-[658.32px] top-[390.31px] bg-[#98b0ff] blur-[147px] origin-top-left rotate-[-18.579deg]">
  </div>

  <div
    class="absolute w-[260.74px] h-[54.57px] left-[969px] top-[-8.93px] bg-[#98b0ff]/40 blur-[47.7px] origin-top-left rotate-[-18.579deg]">
  </div>

  <div
    class="absolute w-[315.56px] h-[161.15px] left-[998.32px] top-[1190.31px] bg-[#98b0ff] blur-[97px] origin-top-left rotate-[-18.579deg]">
  </div>

  <div
    class="absolute w-[560.74px] h-[184.57px] left-[0px] top-[1108.93px] bg-[#98b0ff] blur-[87.7px] origin-top-left rotate-[-18.579deg]">
  </div>




  <div
    class="absolute w-[525.08px] h-[82.15px] left-[985.53px] top-[666.46px] bg-[#00c979] blur-[107px] origin-top-left rotate-[-9.961deg]">
  </div>



  <div
    class="absolute w-[153.89px] h-[55.97px] left-[-56px] top-[847.02px] bg-[#00c979] blur-[81px] origin-top-left rotate-[-9.961deg]">
  </div>

  <div
    class="absolute w-[519.35px] h-[94.17px] left-[-219.02px] top-[-118.97px] bg-[#00c979] blur-[147px] origin-top-left rotate-[48.581deg]">
  </div>



  <div
    class="absolute w-[525.08px] h-[82.15px] left-[985.53px] top-[1566.46px] bg-[#98b0ff] blur-[107px] origin-top-left rotate-[-9.961deg]">
  </div>

  <div
    class="absolute w-[153.89px] h-[55.97px] left-[96px] top-[1547.02px] bg-[#00c979] blur-[81px] origin-top-left rotate-[-9.961deg]">
  </div>

  <!-- Container -->
  <div class="relative max-w-6xl mx-auto px-6">

    <!-- Heading -->

    <!-- Top Cards -->




    <!-- Steps Section -->
    <div class="mt-0 text-center max-w-3xl mx-auto">
      <h3 class="text-[26px] md:text-[30px] font-semibold text-gray-800 leading-tight mb-8">
        Why Use AI Email Writer
      </h3>

      <p class="text-gray-600 text-[14px] md:text-[17px] leading-relaxed mb-8">
        The process of writing emails can be time-consuming and often repetitive. The process becomes more tiring,
        especially when the user needs to match a specific tone and clarity. The AI Email Writer tool
        by ExtractMails helps in removing these challenges effectively.  
      </p>

    </div>

    <!-- Steps Grid -->

    <div class="max-w-5xl mx-auto space-y-10">

      <!-- Card 1 -->
      <div class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[1.5fr_0.5fr] items-center gap-8">

        <!-- Text -->
        <div class="">
          <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
            Boost Productivity 
          </h3>

          <p class="text-gray-600 text-sm leading-relaxed mb-4">
            The AI Email Writer helps in generating emails instantly, instead of spending time drafting them. The tool
            can also generate complete drafts instantly, and can also:



          <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
            <li>Handle more conversations in less time.</li>
            <li>Increase daily productivity. </li>
            <li>Reduce repetitive typing. </li>

          </ul>
        </div>

        <!-- Image -->
        <div class="">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-22-2026-09_56_53-AM-1.png"
            alt="Unknown Sender" class="rounded-2xl w-full max-w-sm mx-auto">
        </div>
      </div>


      <!-- Card 2 -->
      <div class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[0.5fr_1.5fr] items-center gap-8">


        <div class="flex-1">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-22-2026-09_58_03-AM-1.png"
            alt="Verify Leads" class="rounded-2xl w-full max-w-sm mx-auto">
        </div>

        <!-- Text -->
        <div class="flex-1">
          <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
            Maintain Professional Communication 
          </h3>

          <p class="text-gray-600 text-sm leading-relaxed mb-4">
            The AI Email Writer tool helps boost your credibility with your email. It prevents miscommunication, as it
            offers a clear and contextual email. The tool ensures that the emails are:  

          </p>



          <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
            <li>Clear and well-structured. </li>
            <li>Grammatically correct. </li>
            <li>Professionally written. </li>
          </ul>
        </div>

        <!-- Image -->

      </div>






      <!-- Card 1 -->
      <div class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[1.5fr_0.5fr] items-center gap-8">

        <!-- Text -->
        <div class="">
          <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
            Improves Response Rates 
          </h3>

          <p class="text-gray-600 text-sm leading-relaxed mb-4">
            A well-written email increases the likelihood of getting replies from the recipient. The emails generated
            using the tool are well structured with clear, concise, and engaging messages.
          </p>

          <p class="text-gray-700 text-sm mb-2">


            The AI Email Writer tool helps in: 


          </p>

          <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
            <li>Increasing engagement rate.</li>
            <li>Improving open and reply rates. </li>
            <li>Building stronger communication.</li>
          </ul>
        </div>

        <!-- Image -->
        <div class="">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-22-2026-09_59_10-AM-1.png"
            alt="Unknown Sender" class="rounded-2xl w-full max-w-sm mx-auto">
        </div>
      </div>


      <!-- Card 2 -->
      <div class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[0.5fr_1.5fr] items-center gap-8">


        <div class="flex-1">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-22-2026-10_00_24-AM-1.png"
            alt="Verify Leads" class="rounded-2xl w-full max-w-sm mx-auto">
        </div>

        <!-- Text -->
        <div class="flex-1">
          <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
            Reduce Errors 
          </h3>

          <p class="text-gray-600 text-sm leading-relaxed mb-4">
            The AI Email Writer tool helps in eliminating common writing mistakes. A small mistake in tone or grammar
            can affect how the email is perceived. The tool eliminates common mistakes like: 
          </p>

          <p class="text-gray-700 text-sm mb-2">
            The tool helps in ensuring: 

          </p>

          <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
            <li>Grammar errors. </li>
            <li>Poor sentence structure.</li>
            <li>Inconsistent tone.  </li>
          </ul>
        </div>

        <!-- Image -->

      </div>

    </div>



  </div>
</section>


<section class="py-6">

  <!-- TOP SECTION -->
  <div class="max-w-[1500px] mx-auto bg-white px-6 py-2 text-center rounded-lg">

    <h2
      class="text-[44px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent mb-8">
      How AI Email Writer Works?
    </h2>

    <p class="text-gray-600 max-w-3xl mx-auto text-sm md:text-base leading-relaxed">
      The AI Email Writer tool uses advanced language models that understand the input and generate clear and
      high-quality email content. The process of generating emails is powered by intelligent systems, which help in
      curating contextual emails with customizable tones.
    </p>

    <p class="text-gray-600 max-w-3xl mx-auto text-sm md:text-base leading-relaxed mt-2">
      The AI Email Writer tool follows the given process to generate emails:
    </p>



    <!-- STEPS -->
    <div class="relative mt-12">

      <div class="absolute top-1/2 left-0 w-full border-t-2 border-dashed border-gray-800 -translate-y-1/2"></div>

      <div class="relative flex justify-evenly items-center">

        <!-- Step 1 -->
        <div class="step flex flex-col items-center cursor-pointer mt-3 " data-step="1">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            1
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt="icon" class="arrow w-4 h-4 mt-1">
        </div>

        <!-- Step 2 -->
        <div class="step flex flex-col items-center cursor-pointer" data-step="2">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            2
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt="icon"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>

        <!-- Step 3 -->
        <div class="step flex flex-col items-center cursor-pointer" data-step="3">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            3
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt="icon"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>

        <!-- Step 4 -->
        <div class="step flex flex-col items-center cursor-pointer" data-step="4">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            4
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt="icon"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>

        <!-- Step 5 -->
        <div class="step flex flex-col items-center cursor-pointer" data-step="5">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            5
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt="icon"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>
        <div class="step flex flex-col items-center cursor-pointer" data-step="6">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            6
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt="icon"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>

        <div class="step flex flex-col items-center cursor-pointer" data-step="7">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            7
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt="icon"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>

      </div>
    </div>

  </div>

  <!-- BOTTOM CONTENT -->
  <div class="max-w-[1300px] mx-auto px-6 mt-10">

    <!-- Step 1 -->

    <div class="step-content" data-content="1">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Enter the Requirement:
        </h3>
        <p class="px-4">
          The process starts with the user describing their requirements, for example, "Send a proposal to a client" or
          “Write a follow-up email after a meeting”.
        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>

    </div>

    <!-- Step 2 -->
    <div class="step-content hidden" data-content="2">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Understanding the Content and Intent:
        </h3>
        <p class="px-4">
          In the second step, the AI analyzes and determines the purpose, target audience, and desired tone of the
          email.
        </p>

        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>

    <!-- Step 3 -->
    <div class="step-content hidden" data-content="3">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Email Content Generation:
        </h3>
        <p class="px-4">
          The AI generates a complete email draft, based on the input of the user, which includes the subject line,
          opening, & body text, and closing statement.
        </p>

        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>

    <!-- Step 4 -->
    <div class="step-content hidden" data-content="4">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Apply Tone and Style Adjustments:
        </h3>
        <p class="px-4">
          Based on the user's needs, the tool adapts to the tone and style. The user can also further adjust the tone of
          the email with simple instructions.
        </p>

        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>

    <!-- Step 5 -->
    <div class="step-content hidden" data-content="5">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Provide Multiple Variations:
        </h3>
        <p class="px-4">
          The AI generates multiple variations of the email, offering the user the choice of the best option, combining
          multiple ideas, and optimizing messages.

        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>


    <div class="step-content hidden" data-content="6">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Instantly Edit and Refine:
        </h3>
        <p class="px-4">
          The user can make changes to the generated email, which includes:
        </p>
        <ul style="list-style-type: disc; padding-left: 20px;">
          <li>Shortening or expanding content. </li>
          <li>Improving my grammar. </li>
          <li>Adjusting tone. </li>
        </ul>

        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>



    <div class="step-content hidden" data-content="7">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Finalize and Send:
        </h3>
        <p class="px-4">
          Once the user is satisfied with the generated email, they can copy or send the email directly.
        </p>

        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>





  </div>

</section>


<!-- JS -->
<script>
  const steps = document.querySelectorAll('.step');
  const contents = document.querySelectorAll('.step-content');

  steps.forEach(step => {
    step.addEventListener('click', () => {
      const id = step.dataset.step;

      // remove mt-3 from all steps
      steps.forEach(s => s.classList.remove('mt-3'));

      // add mt-3 only to active step
      step.classList.add('mt-3');

      // hide all contents
      contents.forEach(c => c.classList.add('hidden'));

      // show selected content
      document.querySelector(`[data-content="${id}"]`).classList.remove('hidden');

      // hide all arrows
      document.querySelectorAll('.arrow').forEach(a => a.classList.add('hidden'));

      // show active arrow
      step.querySelector('.arrow').classList.remove('hidden');
    });
  });
</script>


<section class=" py-10">
  <div class="max-w-[1300px] mx-auto px-4">

    <!-- Title -->


    <h2 class="text-center text-[32px] md:text-[40px] lg:text-[45px] font-semibold 
  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
  bg-clip-text text-transparent mb-12">
      Other Tools
    </h2>

    <!-- Card -->
    <div class="bg-white border border-gray-300 rounded-xl p-8">

      <!-- Grid -->
      <div class="grid md:grid-cols-2 gap-8 relative">

        <!-- Vertical Divider -->
        <div class="hidden md:block absolute left-1/2 top-0 h-full w-px bg-gray-300"></div>

        <!-- Left Column -->
        <div class="space-y-8 pr-4 md:pr-8">

          <!-- Item 1 -->
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">
              Gmail Generator
            </h3>
            <p class="text-sm text-gray-600 leading-relaxed">
              Create custom or temporary Gmail addresses, which are ideal for testing and quick usage with the
              <a href="https://www.extractmails.com/tools/gmail-generator/" class="text-indigo-600 hover:underline">
                Gmail Generator tool ›
              </a>
            </p>
          </div>

          <!-- Item 2 -->
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">
              Email Spam Checker
            </h3>
            <p class="text-sm text-gray-600 leading-relaxed">
              Analyze any emails for spam triggers and improve your deliverability instantly with
              <a href="https://www.extractmails.com/tools/email-spam-checker/" class="text-indigo-600 hover:underline">
                Email Spam Checker ›
              </a>
            </p>
          </div>

        </div>

        <!-- Right Column -->
        <div class="space-y-8 pl-4 md:pl-8">

          <!-- Item 3 -->
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">
              Reverse Email Lookup
            </h3>
            <p class="text-sm text-gray-600 leading-relaxed">
              Find the details of any email address and convert email into actionable insights with the
              <a href="https://www.extractmails.com/tools/reverse-email-lookup/" class="text-indigo-600 hover:underline">
                Reverse Email Lookup tool ›
              </a>
            </p>
          </div>

          <!-- Item 4 -->
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">
              Email Checker
            </h3>
            <p class="text-sm text-gray-600 leading-relaxed">
              Verify the validity of an email, along with its format and activity with
              <a href="#" class="text-indigo-600 hover:underline">
                Email Checker ›
              </a>
            </p>
          </div>

        </div>

      </div>

    </div>
  </div>
</section>


<section class="max-w-[1400px] mx-auto py-0 px-6">

  <!-- Heading -->
  <h2 class="text-center text-[32px] md:text-[40px] lg:text-[45px] font-semibold 
  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
  bg-clip-text text-transparent mb-12">
    Frequently Asked Questions
  </h2>

  <div class="space-y-6">

    <!-- ITEM -->
    <div class="faq-item cursor-pointer">

      <!-- GRADIENT BORDER WRAPPER -->
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">

        <!-- INNER CARD -->
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">

            <!-- GRADIENT TEXT -->
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Is the AI Email Writer free to use?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <!-- CONTENT -->
          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the AI Email Writer tool offers a free version, which allows unlimited email generation of structure
              and context-aware emails. The basic or free version offers tone adjustment, email generation, and grammar
              & structure correction.
            </p>
          </div>

        </div>
      </div>
    </div>

    <!-- COPY SAME STRUCTURE FOR ALL ITEMS -->

    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Can the tool write replies to the emails?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the AI Email Writer tool is designed to write replies to emails. The AI Email Writer tool analyzes
              the origin of the message to generate relevant and contextual responses. The tool also generates highly
              personalized and professional emails, with clear and concise content.
            </p>
          </div>

        </div>
      </div>
    </div>



    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Does the tool generate unique emails?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the AI Email Writer tool can generate unique and tailored emails. The tool leverages advanced AI
              models, like LLM, to create content based on specific prompts provided by the users. The tool generates an
              email based on the tone, context, and intent of the user.
            </p>
          </div>

        </div>
      </div>
    </div>





    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Is my data safe?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the AI Email Writer tool does not store the data of the users. The data provided by the users is
              processed securely and is not stored permanently, ensuring privacy and confidentiality.
            </p>
          </div>

        </div>
      </div>
    </div>

    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Is the tool beginner-friendly?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the AI Email Writer tool by ExtractMails is user-friendly. The tool requires no technical knowledge
              or writing expertise. It works simply by entering a prompt and checking the tone and structure of the
              email.
            </p>
          </div>

        </div>
      </div>
    </div>


    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Does the tool also generate the subject line?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the AI Email Writer tool generally generates the subject lines, along with the email body. The tool
              analyzes the prompt or context to create engaging and concise subject lines.
          </div>

        </div>
      </div>
    </div>

    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Can the tool generate multiple versions of an email?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the AI Email Writer tool can generate multiple versions of an email. The tool often creates different
              tones, lengths, and strategic approaches from a single prompt.
            </p>
          </div>

        </div>
      </div>
    </div>


    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              How is the tool beneficial for businesses?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              The AI Email Writer tool helps businesses automate the drafting and personalizing of the tone and
              structure of the email. It also helps in improving the efficiency of the email and generating professional
              responses, cold outreach, and follow-ups.
            </p>
          </div>

        </div>
      </div>
    </div>


  </div>

</section>


<script>
  document.querySelectorAll('.faq-item').forEach(item => {
    item.addEventListener('click', () => {

      const content = item.querySelector('.faq-content');
      const icon = item.querySelector('.faq-icon');

      // Close others
      document.querySelectorAll('.faq-item').forEach(i => {
        if (i !== item) {
          const c = i.querySelector('.faq-content');
          c.style.maxHeight = null;
          i.querySelector('.faq-icon').textContent = '+';
        }
      });

      // Toggle current
      if (content.style.maxHeight) {
        content.style.maxHeight = null;
        icon.textContent = '+';
      } else {
        content.style.maxHeight = content.scrollHeight + "px";
        icon.textContent = '-';
      }

    });
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
	if (window.history && typeof window.history.replaceState === 'function') {
		window.history.replaceState(null, '', window.location.href);
	}

	const form = document.getElementById('extractmail-ai-writer-form');
	const brief = document.getElementById('extractmail-ai-brief');
	const submitButton = document.getElementById('extractmail-ai-writer-submit');
	const errorBox = document.getElementById('extractmail-ai-writer-error');
	const loadingBox = document.getElementById('extractmail-ai-loading');
	const resultBox = document.getElementById('extractmail-ai-result');
	const copyButton = document.getElementById('extractmail-ai-copy');
	const copyDefaultIcon = copyButton ? copyButton.querySelector('.extractmail-ai-copy-default') : null;
	const copySuccessIcon = copyButton ? copyButton.querySelector('.extractmail-ai-copy-success') : null;
	const tryNowLink = document.querySelector('.extractmail-try-now-link');
	const writerSection = document.getElementById('extractmail-ai-writer-section');
	const defaultResultText = 'Your email content will appear here.';

	if (tryNowLink && writerSection) {
		tryNowLink.addEventListener('click', function (event) {
			const header = document.querySelector('.site-header, #masthead, header');
			const headerHeight = header ? header.getBoundingClientRect().height : 0;
			const scrollGap = Math.max(headerHeight + 28, 110);
			const targetTop = writerSection.getBoundingClientRect().top + window.pageYOffset - scrollGap;

			event.preventDefault();
			window.scrollTo({
				top: Math.max(targetTop, 0),
				behavior: 'smooth'
			});
		});
	}

	if (!form || !brief || !submitButton || !errorBox || !loadingBox || !resultBox || !copyButton || !copyDefaultIcon || !copySuccessIcon) {
		return;
	}

	const defaultButtonMarkup = submitButton.innerHTML;
	let isSubmitting = false;

	function showError(message) {
		errorBox.textContent = message;
		errorBox.style.display = 'block';
	}

	function hideError() {
		errorBox.textContent = '';
		errorBox.style.display = 'none';
	}

	function setLoading(isLoading) {
		if (isLoading) {
			loadingBox.style.display = 'block';
			resultBox.style.display = 'none';
			submitButton.disabled = true;
			submitButton.style.opacity = '0.85';
			submitButton.style.cursor = 'wait';
		} else {
			loadingBox.style.display = 'none';
			resultBox.style.display = '';
			submitButton.disabled = false;
			submitButton.style.opacity = '1';
			submitButton.style.cursor = 'pointer';
		}
	}

	function setCopySuccess(isSuccess) {
		if (isSuccess) {
			copyDefaultIcon.style.display = 'none';
			copySuccessIcon.style.display = 'inline-flex';
		} else {
			copyDefaultIcon.style.display = 'inline-flex';
			copySuccessIcon.style.display = 'none';
		}
	}

	function fallbackCopyText(text) {
		const helper = document.createElement('textarea');
		helper.value = text;
		helper.setAttribute('readonly', '');
		helper.style.position = 'fixed';
		helper.style.opacity = '0';
		helper.style.pointerEvents = 'none';
		helper.style.left = '-9999px';
		document.body.appendChild(helper);
		helper.focus();
		helper.select();

		let copied = false;
		try {
			copied = document.execCommand('copy');
		} catch (error) {
			copied = false;
		}

		document.body.removeChild(helper);
		return copied;
	}

	function copyText(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text).then(function () {
				return true;
			}).catch(function () {
				return fallbackCopyText(text);
			});
		}

		return Promise.resolve(fallbackCopyText(text));
	}

	function updateCopyButton() {
		const value = resultBox.textContent.trim();

		if (value && value !== defaultResultText) {
			copyButton.style.display = 'inline-flex';
			copyButton.style.alignItems = 'center';
			copyButton.style.justifyContent = 'center';
		} else {
			copyButton.style.display = 'none';
		}

		setCopySuccess(false);
	}

	function resetInterface() {
		form.reset();
		brief.value = '';
		resultBox.textContent = defaultResultText;
		hideError();
		setLoading(false);
		updateCopyButton();
	}

	function isReloadNavigation() {
		if (window.performance) {
			if (typeof window.performance.getEntriesByType === 'function') {
				const entries = window.performance.getEntriesByType('navigation');
				if (entries && entries.length && entries[0].type === 'reload') {
					return true;
				}
			}

			if (window.performance.navigation && window.performance.navigation.type === 1) {
				return true;
			}
		}

		return false;
	}

	form.addEventListener('submit', function (event) {
		event.preventDefault();

		if (isSubmitting) {
			return;
		}

		hideError();

		if (!brief.value.trim()) {
			showError('Please describe the email you want to write.');
			return;
		}

		isSubmitting = true;
		setLoading(true);

		const body = new URLSearchParams(new FormData(form));

		fetch(window.location.href, {
			method: 'POST',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
				'Accept': 'application/json'
			},
			credentials: 'same-origin',
			body: body.toString()
		})
			.then(function (response) {
				if (!response.ok) {
					throw new Error('Request failed.');
				}

				return response.json();
			})
			.then(function (payload) {
				if (!payload) {
					throw new Error('Empty response.');
				}

				if (!payload.success) {
					const message = payload.data && payload.data.message ? payload.data.message : 'The email draft could not be generated.';
					throw new Error(message);
				}

				if (!payload.data || !payload.data.result || !payload.data.result.text) {
					throw new Error('Azure OpenAI did not return any email text.');
				}

				resultBox.textContent = payload.data.result.text;
				updateCopyButton();
			})
			.catch(function (error) {
				showError(error && error.message ? error.message : 'The email draft could not be generated.');
			})
			.finally(function () {
				isSubmitting = false;
				setLoading(false);
				submitButton.innerHTML = defaultButtonMarkup;
			});
	});

	copyButton.addEventListener('click', function () {
		const value = resultBox.textContent.trim();
		if (!value || value === defaultResultText) {
			return;
		}

		copyButton.disabled = true;
		copyText(value).then(function (copied) {
			if (!copied) {
				showError('The email could not be copied. Please try again.');
				return;
			}

			setCopySuccess(true);
			window.setTimeout(function () {
				setCopySuccess(false);
			}, 1400);
		}).finally(function () {
			copyButton.disabled = false;
		});
	});

	if (isReloadNavigation()) {
		resetInterface();
	}

	window.addEventListener('pageshow', function (event) {
		if (event.persisted) {
			resetInterface();
		}
	});

	updateCopyButton();
});
</script>


<style id="faq-style">
  .faq-content {
    transition: all 0.3s ease;
  }
</style>










<?php get_footer() ?>
