<?php
/**
 * Template Name: Apollo Reverse Email Lookup Tool
 * Template Post Type: page
 * Description: Tailwind-powered reverse email lookup page powered by Apollo People Enrichment.
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! function_exists('extractmail_apollo_reverse_lookup_get_api_key')) {
    function extractmail_apollo_reverse_lookup_get_api_key()
    {
        if (defined('EXTRACTMAIL_APOLLO_API_KEY') && EXTRACTMAIL_APOLLO_API_KEY) {
            return EXTRACTMAIL_APOLLO_API_KEY;
        }

        if (defined('APOLLO_API_KEY') && APOLLO_API_KEY) {
            return APOLLO_API_KEY;
        }

        $env_key = getenv('EXTRACTMAIL_APOLLO_API_KEY');
        if ($env_key) {
            return $env_key;
        }

        $env_key = getenv('APOLLO_API_KEY');
        if ($env_key) {
            return $env_key;
        }

        return '';
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_is_ajax_request')) {
    function extractmail_apollo_reverse_lookup_is_ajax_request()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' === strtolower(wp_unslash($_SERVER['HTTP_X_REQUESTED_WITH']));
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_get_error_message_from_body')) {
    function extractmail_apollo_reverse_lookup_get_error_message_from_body($body, $fallback)
    {
        if (! is_array($body)) {
            return $fallback;
        }

        $candidates = array(
            $body['error'] ?? null,
            $body['message'] ?? null,
            $body['detail'] ?? null,
            $body['details'] ?? null,
            $body['errors'][0]['message'] ?? null,
            $body['errors'][0]['detail'] ?? null,
            $body['errors'][0]['details'] ?? null,
        );

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && '' !== trim($candidate)) {
                return trim($candidate);
            }
        }

        return $fallback;
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_has_value')) {
    function extractmail_apollo_reverse_lookup_has_value($value)
    {
        if (is_array($value)) {
            return ! empty(array_filter($value, 'extractmail_apollo_reverse_lookup_has_value'));
        }

        return null !== $value && '' !== trim((string) $value);
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_normalize_url')) {
    function extractmail_apollo_reverse_lookup_normalize_url($url)
    {
        $url = trim((string) $url);
        if ('' === $url) {
            return '';
        }

        if (0 === strpos($url, 'http://') || 0 === strpos($url, 'https://')) {
            return $url;
        }

        return 'https://' . ltrim($url, '/');
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_get_url_label')) {
    function extractmail_apollo_reverse_lookup_get_url_label($url)
    {
        $url = extractmail_apollo_reverse_lookup_normalize_url($url);
        if ('' === $url) {
            return '';
        }

        $host = wp_parse_url($url, PHP_URL_HOST);
        if (! is_string($host) || '' === $host) {
            return $url;
        }

        return preg_replace('/^www\./i', '', $host);
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_get_initials')) {
    function extractmail_apollo_reverse_lookup_get_initials($value)
    {
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));
        if ('' === $value) {
            return '?';
        }

        $parts    = preg_split('/\s+/', $value);
        $initials = '';

        foreach ($parts as $part) {
            if ('' === $part) {
                continue;
            }

            $initials .= strtoupper(substr($part, 0, 1));

            if (strlen($initials) >= 2) {
                break;
            }
        }

        return '' !== $initials ? $initials : strtoupper(substr($value, 0, 1));
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_build_location')) {
    function extractmail_apollo_reverse_lookup_build_location($parts)
    {
        $parts = is_array($parts) ? $parts : array( $parts );
        $parts = array_values(
            array_filter(
                array_map(
                    static function ($value) {
                        return trim((string) $value);
                    },
                    $parts
                )
            )
        );

        return implode(', ', $parts);
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_get_current_employment')) {
    function extractmail_apollo_reverse_lookup_get_current_employment($person)
    {
        if (empty($person['employment_history']) || ! is_array($person['employment_history'])) {
            return array();
        }

        foreach ($person['employment_history'] as $employment) {
            if (! empty($employment['current']) && is_array($employment)) {
                return $employment;
            }
        }

        return is_array($person['employment_history'][0] ?? null) ? $person['employment_history'][0] : array();
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_call_api')) {
    function extractmail_apollo_reverse_lookup_call_api($email)
    {
        $api_key = extractmail_apollo_reverse_lookup_get_api_key();

        if ('' === $api_key) {
            return new WP_Error('apollo_missing_key', 'The lookup API key is missing. Add EXTRACTMAIL_APOLLO_API_KEY to wp-config.php or the server environment.');
        }

        $url = add_query_arg(
            array(
                'email'                  => $email,
                'reveal_personal_emails' => 'false',
                'reveal_phone_number'    => 'false',
                'run_waterfall_email'    => 'false',
                'run_waterfall_phone'    => 'false',
            ),
            'https://api.apollo.io/api/v1/people/match'
        );

        $response = wp_remote_post(
            $url,
            array(
                'timeout'   => 20,
                'sslverify' => true,
                'headers'   => array(
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Cache-Control' => 'no-cache',
                    'X-Api-Key'     => $api_key,
                ),
                'body'      => wp_json_encode((object) array()),
            )
        );

        if (is_wp_error($response)) {
            return $response;
        }

        $status = (int) wp_remote_retrieve_response_code($response);
        $body   = json_decode((string) wp_remote_retrieve_body($response), true);

        if (200 === $status) {
            if (! is_array($body)) {
                return new WP_Error('apollo_invalid_response', 'The lookup service returned an invalid response for this request.');
            }

            if (empty($body['person']) || ! is_array($body['person'])) {
                return array(
                    'status'  => 'not_found',
                    'message' => 'No matched person record was returned for this email address.',
                );
            }

            return array(
                'status' => 'found',
                'data'   => $body,
            );
        }

        if (401 === $status) {
            return new WP_Error('apollo_unauthorized', 'The lookup API key was rejected. Please verify EXTRACTMAIL_APOLLO_API_KEY and try again.');
        }

        if (403 === $status) {
            return new WP_Error('apollo_forbidden', 'The lookup request was denied. Please confirm the API key has the required access.');
        }

        if (422 === $status) {
            return new WP_Error('apollo_validation', extractmail_apollo_reverse_lookup_get_error_message_from_body($body, 'This lookup request could not be processed. Please confirm the email address and try again.'));
        }

        if (429 === $status) {
            return new WP_Error('apollo_rate_limited', 'The lookup service is rate-limiting requests right now. Please wait a moment and try again.');
        }

        $fallback = 'The lookup service returned an unexpected HTTP status: ' . $status . '.';
        return new WP_Error('apollo_http_error', extractmail_apollo_reverse_lookup_get_error_message_from_body($body, $fallback));
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_render_detail_item')) {
    function extractmail_apollo_reverse_lookup_render_detail_item($label, $value, $is_link = false, $display_value = '')
    {
        if (! extractmail_apollo_reverse_lookup_has_value($value)) {
            return '';
        }

        $display_value = '' !== trim((string) $display_value) ? trim((string) $display_value) : trim((string) $value);

        ob_start();
        ?>
<div class="rounded-[20px] border border-line bg-white/90 px-4 py-3 shadow-sm shadow-slate-100/70">
	<div class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-400">
		<?php echo esc_html($label); ?>
	</div>
	<div class="mt-1 text-[15px] font-semibold leading-6 text-ink break-words">
		<?php if ($is_link) : ?>
		<a class="text-brand hover:text-brand-dark"
			href="<?php echo esc_url($value); ?>" target="_blank"
			rel="noreferrer noopener">
			<?php echo esc_html($display_value); ?>
		</a>
		<?php else : ?>
		<?php echo esc_html($display_value); ?>
		<?php endif; ?>
	</div>
</div>
<?php

        return (string) ob_get_clean();
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_render_social_icon')) {
    function extractmail_apollo_reverse_lookup_render_social_icon($label)
    {
        $label = strtolower(trim((string) $label));

        ob_start();

        if ('linkedin' === $label) :
            ?>
<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="currentColor">
	<path
		d="M4.98 3.5a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM3 8.75h3.96V21H3V8.75Zm6.21 0h3.8v1.67h.05c.53-1 1.84-2.06 3.79-2.06 4.05 0 4.8 2.66 4.8 6.11V21h-3.96v-5.73c0-1.37-.02-3.13-1.91-3.13-1.91 0-2.2 1.49-2.2 3.03V21H9.21V8.75Z">
	</path>
</svg>
<?php
        elseif ('x' === $label) :
            ?>
<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="currentColor">
	<path
		d="M18.9 2H22l-6.77 7.74L23 22h-6.1l-4.78-6.24L6.66 22H3.55l7.24-8.28L1 2h6.25l4.31 5.7L18.9 2Zm-1.07 18h1.69L6.33 3.9H4.52L17.83 20Z">
	</path>
</svg>
<?php
        elseif ('github' === $label) :
            ?>
<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="currentColor">
	<path
		d="M12 .5a12 12 0 0 0-3.79 23.39c.6.11.82-.26.82-.58v-2.03c-3.34.72-4.04-1.42-4.04-1.42-.55-1.37-1.33-1.73-1.33-1.73-1.08-.74.08-.73.08-.73 1.19.08 1.82 1.21 1.82 1.21 1.06 1.79 2.77 1.28 3.45.98.11-.75.41-1.28.74-1.57-2.67-.3-5.48-1.31-5.48-5.86 0-1.3.47-2.36 1.23-3.19-.12-.3-.53-1.53.12-3.18 0 0 1.01-.32 3.3 1.22a11.56 11.56 0 0 1 6 0c2.28-1.54 3.29-1.22 3.29-1.22.65 1.65.24 2.88.12 3.18.77.83 1.23 1.89 1.23 3.19 0 4.56-2.81 5.55-5.49 5.85.43.37.81 1.1.81 2.22v3.3c0 .32.21.7.83.58A12 12 0 0 0 12 .5Z">
	</path>
</svg>
<?php
        elseif ('facebook' === $label) :
            ?>
<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="currentColor">
	<path
		d="M13.5 21v-7.12H16l.38-2.88h-2.88V9.16c0-.83.23-1.4 1.43-1.4h1.53V5.18c-.26-.04-1.17-.11-2.23-.11-2.2 0-3.71 1.34-3.71 3.81V11H8v2.88h2.52V21h2.98Z">
	</path>
</svg>
<?php
        else :
            ?>
<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"
	stroke-linecap="round" stroke-linejoin="round">
	<path d="M10 13a5 5 0 0 0 7.54.54l2-2a5 5 0 0 0-7.07-7.07l-1.15 1.14"></path>
	<path d="M14 11a5 5 0 0 0-7.54-.54l-2 2a5 5 0 0 0 7.07 7.07l1.15-1.14"></path>
</svg>
<?php
        endif;

        return (string) ob_get_clean();
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_render_social_link')) {
    function extractmail_apollo_reverse_lookup_render_social_link($label, $url)
    {
        $url = extractmail_apollo_reverse_lookup_normalize_url($url);
        if ('' === $url) {
            return '';
        }

        ob_start();
        ?>
<a class="inline-flex items-center gap-2 rounded-full border border-line bg-white px-3.5 py-2 text-sm font-semibold text-ink transition hover:-translate-y-0.5 hover:border-brand hover:bg-brand hover:text-white"
	href="<?php echo esc_url($url); ?>" target="_blank"
	rel="noreferrer noopener">
	<span class="inline-flex h-5 w-5 items-center justify-center">
		<?php echo extractmail_apollo_reverse_lookup_render_social_icon($label); ?>
	</span>
	<?php echo esc_html($label); ?>
</a>
<?php

        return (string) ob_get_clean();
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_render_status_icon')) {
    function extractmail_apollo_reverse_lookup_render_status_icon($status)
    {
        ob_start();
        ?>
<div
	class="flex h-[70px] w-[70px] shrink-0 items-center justify-center rounded-full shadow-sm ring-4 ring-white <?php echo esc_attr('found' === $status ? 'bg-emerald-500 text-white' : ('not_found' === $status ? 'bg-amber-400 text-white' : 'bg-slate-400 text-white')); ?>">
	<?php if ('found' === $status) : ?>
	<svg viewBox="0 0 24 24" width="30" height="30" aria-hidden="true" fill="none" stroke="currentColor"
		stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
		<path d="M5 12.5l4.2 4.2L19 7.4"></path>
	</svg>
	<?php elseif ('not_found' === $status) : ?>
	<svg viewBox="0 0 24 24" width="30" height="30" aria-hidden="true" fill="none" stroke="currentColor"
		stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
		<path d="M10.5 10.5a2.5 2.5 0 1 1 4.5 1.5c-.8.9-1.8 1.5-2.4 2.1-.4.4-.6.8-.6 1.4"></path>
		<circle cx="12" cy="17.4" r="1"></circle>
	</svg>
	<?php else : ?>
	<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" fill="none" stroke="currentColor"
		stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
		<path d="M7 7l10 10"></path>
		<path d="M17 7L7 17"></path>
	</svg>
	<?php endif; ?>
</div>
<?php

        return (string) ob_get_clean();
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_render_person_card')) {
    function extractmail_apollo_reverse_lookup_render_person_card($person)
    {
        if (empty($person) || ! is_array($person)) {
            return '';
        }

        $employment         = extractmail_apollo_reverse_lookup_get_current_employment($person);
        $full_name          = trim((string) ($person['name'] ?? ''));
        $email              = trim((string) ($person['email'] ?? ''));
        $title              = trim((string) ($person['title'] ?? ($employment['title'] ?? '')));
        $headline           = trim((string) ($person['headline'] ?? ''));
        $organization_name  = trim((string) ($employment['organization_name'] ?? ($person['organization']['name'] ?? '')));
        $location           = extractmail_apollo_reverse_lookup_build_location(
            array(
                $person['city'] ?? '',
                $person['state'] ?? '',
                $person['country'] ?? '',
            )
        );
        $linkedin_url       = trim((string) ($person['linkedin_url'] ?? ''));
        $twitter_url        = trim((string) ($person['twitter_url'] ?? ''));
        $github_url         = trim((string) ($person['github_url'] ?? ''));
        $facebook_url       = trim((string) ($person['facebook_url'] ?? ''));
        $email_status       = trim((string) ($person['email_status'] ?? ''));
        $confidence         = $person['extrapolated_email_confidence'] ?? '';
        $avatar             = trim((string) ($person['photo_url'] ?? ''));
        $initials           = extractmail_apollo_reverse_lookup_get_initials('' !== $full_name ? $full_name : $email);
        $confidence_display = '' !== (string) $confidence ? (string) $confidence : '';

        ob_start();
        ?>
<div class="rounded-[28px] border border-line bg-gradient-to-br from-white via-white to-slate-50 p-6 shadow-sm sm:p-7">
	<div class="flex items-start gap-4">
		<?php if ('' !== $avatar) : ?>
		<img src="<?php echo esc_url($avatar); ?>" alt=""
			class="h-16 w-16 rounded-2xl object-cover" />
		<?php else : ?>
		<div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-panel text-xl font-extrabold text-brand">
			<?php echo esc_html($initials); ?>
		</div>
		<?php endif; ?>
		<div class="min-w-0">
			<p class="text-sm font-extrabold uppercase tracking-[0.24em] text-brand">Person</p>
			<h3 class="mt-2 text-[28px] font-extrabold leading-tight text-ink">
				<?php echo esc_html('' !== $full_name ? $full_name : $email); ?>
			</h3>
			<?php if ('' !== $title || '' !== $organization_name) : ?>
			<p class="mt-2 text-[16px] leading-7 text-slate-600">
				<?php echo esc_html(trim($title . ('' !== $title && '' !== $organization_name ? ' at ' : '') . $organization_name)); ?>
			</p>
			<?php endif; ?>
			<?php if ('' !== $headline) : ?>
			<p class="mt-2 text-[15px] leading-7 text-slate-500">
				<?php echo esc_html($headline); ?>
			</p>
			<?php endif; ?>
			<?php if ('' !== $email_status || '' !== $confidence_display) : ?>
			<div class="mt-3 flex flex-wrap gap-2">
				<?php if ('' !== $email_status) : ?>
				<span
					class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">
					<?php echo esc_html($email_status); ?>
				</span>
				<?php endif; ?>
				<?php if ('' !== $confidence_display) : ?>
				<span
					class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-brand">
					Confidence
					<?php echo esc_html($confidence_display); ?>
				</span>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="mt-6 grid gap-3 sm:grid-cols-2">
		<?php echo extractmail_apollo_reverse_lookup_render_detail_item('Email', $email); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_detail_item('Location', $location); ?>
	</div>

	<div class="mt-5 flex flex-wrap gap-2">
		<?php echo extractmail_apollo_reverse_lookup_render_social_link('LinkedIn', $linkedin_url); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_social_link('X', $twitter_url); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_social_link('GitHub', $github_url); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_social_link('Facebook', $facebook_url); ?>
	</div>
</div>
<?php

        return (string) ob_get_clean();
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_render_company_card')) {
    function extractmail_apollo_reverse_lookup_render_company_card($company)
    {
        if (empty($company) || ! is_array($company)) {
            return '';
        }

        $name         = trim((string) ($company['name'] ?? ''));
        $domain       = trim((string) ($company['primary_domain'] ?? ''));
        $website_url  = extractmail_apollo_reverse_lookup_normalize_url($company['website_url'] ?? '');
        $industry     = trim((string) ($company['industry'] ?? ''));
        $employees    = trim((string) ($company['estimated_num_employees'] ?? ''));
        $founded      = trim((string) ($company['founded_year'] ?? ''));
        $revenue      = trim((string) ($company['organization_revenue_printed'] ?? ''));
        $phone        = trim((string) ($company['phone'] ?? ''));
        $location     = extractmail_apollo_reverse_lookup_build_location(
            array(
                $company['city'] ?? '',
                $company['state'] ?? '',
                $company['country'] ?? '',
            )
        );
        $linkedin_url = trim((string) ($company['linkedin_url'] ?? ''));
        $twitter_url  = trim((string) ($company['twitter_url'] ?? ''));
        $facebook_url = trim((string) ($company['facebook_url'] ?? ''));
        $logo_url     = trim((string) ($company['logo_url'] ?? ''));
        $initials     = extractmail_apollo_reverse_lookup_get_initials('' !== $name ? $name : $domain);
        $website_label = extractmail_apollo_reverse_lookup_get_url_label($website_url);
        $keywords     = array();

        if (! empty($company['keywords']) && is_array($company['keywords'])) {
            $keywords = array_slice(
                array_values(
                    array_filter(
                        array_map(
                            static function ($keyword) {
                                return trim((string) $keyword);
                            },
                            $company['keywords']
                        )
                    )
                ),
                0,
                6
            );
        }

        ob_start();
        ?>
<div class="rounded-[28px] border border-line bg-gradient-to-br from-white via-white to-slate-50 p-6 shadow-sm sm:p-7">
	<div class="flex items-start gap-4">
		<?php if ('' !== $logo_url) : ?>
		<img src="<?php echo esc_url($logo_url); ?>" alt=""
			class="h-16 w-16 rounded-2xl border border-line bg-white object-contain p-2" />
		<?php else : ?>
		<div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-panel text-xl font-extrabold text-brand">
			<?php echo esc_html($initials); ?>
		</div>
		<?php endif; ?>
		<div class="min-w-0">
			<p class="text-sm font-extrabold uppercase tracking-[0.24em] text-brand">Company</p>
			<h3 class="mt-2 text-[28px] font-extrabold leading-tight text-ink">
				<?php echo esc_html('' !== $name ? $name : $domain); ?>
			</h3>
			<?php if ('' !== $domain) : ?>
			<p class="mt-2 text-[16px] leading-7 text-slate-600">
				<?php echo esc_html($domain); ?>
			</p>
			<?php endif; ?>
		</div>
	</div>

	<div class="mt-6 grid gap-3 sm:grid-cols-2">
		<?php echo extractmail_apollo_reverse_lookup_render_detail_item('Website', $website_url, true, $website_label); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_detail_item('Industry', $industry); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_detail_item('Employees', $employees); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_detail_item('Founded', $founded); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_detail_item('Revenue', $revenue); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_detail_item('Location', $location); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_detail_item('Phone', $phone); ?>
	</div>

	<div class="mt-5 flex flex-wrap gap-2">
		<?php echo extractmail_apollo_reverse_lookup_render_social_link('LinkedIn', $linkedin_url); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_social_link('X', $twitter_url); ?>
		<?php echo extractmail_apollo_reverse_lookup_render_social_link('Facebook', $facebook_url); ?>
	</div>

	<?php if (! empty($keywords)) : ?>
	<div class="mt-5 flex flex-wrap gap-2">
		<?php foreach ($keywords as $keyword) : ?>
		<span class="inline-flex rounded-full bg-panel px-3 py-2 text-sm font-semibold text-slate-600">
			<?php echo esc_html($keyword); ?>
		</span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>
<?php

        return (string) ob_get_clean();
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_render_results')) {
    function extractmail_apollo_reverse_lookup_render_results($result)
    {
        if (empty($result) || ! is_array($result)) {
            return '';
        }

        $status = $result['status'] ?? 'error';
        $email  = trim((string) ($result['email'] ?? ''));
        $title  = 'Lookup unavailable';
        $text   = 'We could not complete this reverse email lookup.';

        if ('found' === $status) {
            $title = 'Match found';
            $text  = 'We found a likely professional match for ' . $email . '.';
        } elseif ('not_found' === $status) {
            $title = 'No match found';
            $text  = trim((string) ($result['message'] ?? ''));
            if ('' === $text) {
                $text = 'We could not find a matched person record for this email address.';
            }
        } elseif ('invalid' === $status) {
            $title = 'Enter a valid email';
            $text  = trim((string) ($result['message'] ?? ''));
        } else {
            $text = trim((string) ($result['message'] ?? $text));
        }

        $person       = isset($result['person']) && is_array($result['person']) ? $result['person'] : array();
        $organization = isset($result['organization']) && is_array($result['organization']) ? $result['organization'] : array();
        $person_html  = extractmail_apollo_reverse_lookup_render_person_card($person);
        $org_html     = extractmail_apollo_reverse_lookup_render_company_card($organization);

        ob_start();
        ?>
<div class="mt-8 overflow-hidden rounded-[30px] border border-line bg-white shadow-sm">
	<div class="border-b border-line bg-gradient-to-r from-panel via-white to-panel px-6 py-5 sm:px-8">
		<div class="flex flex-col gap-5 sm:flex-row sm:items-start">
			<?php echo extractmail_apollo_reverse_lookup_render_status_icon($status); ?>
			<div class="min-w-0">
				<p class="text-sm font-extrabold uppercase tracking-[0.24em] text-brand">Reverse Email Lookup</p>
				<h2 class="mt-2 text-[28px] font-extrabold leading-tight text-ink sm:text-[34px]">
					<?php echo esc_html($title); ?>
				</h2>
				<p class="mt-3 max-w-3xl text-[17px] leading-8 text-slate-600">
					<?php echo esc_html($text); ?>
				</p>
			</div>
		</div>
	</div>

	<?php if ('found' === $status && ('' !== $person_html || '' !== $org_html)) : ?>
	<div class="grid gap-6 p-6 sm:p-8 xl:grid-cols-2">
		<?php echo $person_html; ?>
		<?php echo $org_html; ?>
	</div>
	<?php endif; ?>
</div>
<?php

        return (string) ob_get_clean();
    }
}

if (! function_exists('extractmail_apollo_reverse_lookup_run')) {
    function extractmail_apollo_reverse_lookup_run($email)
    {
        $email = strtolower(trim((string) $email));

        if ('' === $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return array(
                'status'  => 'invalid',
                'email'   => $email,
                'message' => 'Please enter a valid work email address to run the lookup.',
            );
        }

        $apollo = extractmail_apollo_reverse_lookup_call_api($email);

        if (is_wp_error($apollo)) {
            return array(
                'status'  => 'error',
                'email'   => $email,
                'message' => $apollo->get_error_message(),
            );
        }

        if ('not_found' === ($apollo['status'] ?? '')) {
            return array(
                'status'  => 'not_found',
                'email'   => $email,
                'message' => $apollo['message'] ?? '',
            );
        }

        $data   = isset($apollo['data']) && is_array($apollo['data']) ? $apollo['data'] : array();
        $person = isset($data['person']) && is_array($data['person']) ? $data['person'] : array();

        return array(
            'status'       => 'found',
            'email'        => $email,
            'person'       => $person,
            'organization' => isset($person['organization']) && is_array($person['organization']) ? $person['organization'] : array(),
            'raw'          => $data,
        );
    }
}

$extractmail_apollo_reverse_lookup_email  = '';
$extractmail_apollo_reverse_lookup_result = null;

if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['extractmail_apollo_reverse_lookup_submit'])) {
    $extractmail_apollo_reverse_lookup_email = sanitize_email(wp_unslash($_POST['lookup_email'] ?? ''));

    if (! isset($_POST['extractmail_apollo_reverse_lookup_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['extractmail_apollo_reverse_lookup_nonce'])), 'extractmail_apollo_reverse_lookup')) {
        $extractmail_apollo_reverse_lookup_result = array(
            'status'  => 'error',
            'email'   => $extractmail_apollo_reverse_lookup_email,
            'message' => 'Security validation failed. Please refresh the page and try again.',
        );
    } else {
        $extractmail_apollo_reverse_lookup_result = extractmail_apollo_reverse_lookup_run($extractmail_apollo_reverse_lookup_email);
    }

    if (extractmail_apollo_reverse_lookup_is_ajax_request()) {
        wp_send_json_success(
            array(
                'html'   => extractmail_apollo_reverse_lookup_render_results($extractmail_apollo_reverse_lookup_result),
                'result' => $extractmail_apollo_reverse_lookup_result,
            )
        );
    }
}


get_header();
?>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..900;1,9..40,300..900&amp;display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"DM Sans"', 'sans-serif'],
          },
          colors: {
            brand: {
              navy: '#071C50',
              navySec: '#142F68',
              navyDeep: '#051336',
              blue: '#2563EB',
              blueHighlight: '#3B82F6',
              icy: '#F6FAFD',
              pale: '#EAF3FF',
              borderLight: '#D5EAFD',
              textLightMain: '#12213F',
              textLightSec: '#52627B',
              textDarkMain: '#FFFFFF',
              textDarkSec: '#CDDAF4'
            }
          }
        }
      }
    }
  </script>
  <style>
    /* Custom Glass & Backdrop Utilities */
    .glass-navy {
      background: rgba(20, 47, 104, 0.65);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(59, 130, 246, 0.22);
    }
    .glass-navy-subtle {
      background: rgba(15, 36, 85, 0.5);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .glass-white {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border: 1px solid #D5EAFD;
      box-shadow: 0 20px 40px -15px rgba(7, 28, 80, 0.07);
    }
    .glow-radial {
      background: radial-gradient(circle at 60% 40%, rgba(37, 99, 235, 0.25) 0%, rgba(7, 28, 80, 0) 70%);
    }
    .glow-lens {
      background: radial-gradient(circle at center, rgba(59, 130, 246, 0.15) 0%, rgba(7, 28, 80, 0) 70%);
    }
    body { overflow-x: clip; }

        /* === PREMIUM CINEMATIC KEYFRAMES === */
    @keyframes heroPillDrop {
      0% {
        opacity: 0;
        transform: translateY(-16px) scale(0.94);
        filter: blur(8px);
      }
      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
        filter: blur(0px);
      }
    }

    @keyframes heroHeadlineReveal {
      0% {
        opacity: 0;
        transform: translateY(32px) scale(0.97);
        filter: blur(12px);
      }
      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
        filter: blur(0px);
      }
    }

    @keyframes heroTextReveal {
      0% {
        opacity: 0;
        transform: translateY(22px);
        filter: blur(6px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
        filter: blur(0px);
      }
    }

    @keyframes heroBtnSpring {
      0% {
        opacity: 0;
        transform: translateY(24px) scale(0.95);
      }
      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes cinematicCardDock {
      0% {
        opacity: 0;
        transform: perspective(1200px) translateY(36px) rotateX(3deg) scale(0.96);
        filter: blur(12px);
      }
      100% {
        opacity: 1;
        transform: perspective(1200px) translateY(0) rotateX(0deg) scale(1);
        filter: blur(0px);
      }
    }

    @keyframes cardSheenSweep {
      0% { left: -140%; opacity: 0; }
      25% { opacity: 0.85; }
      65% { opacity: 0.85; }
      100% { left: 180%; opacity: 0; }
    }

    /* Hero Left Copy Choreography */
    .hero-pill-badge {
      animation: heroPillDrop 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.08s both;
      will-change: transform, opacity, filter;
    }

    .hero-main-title {
      animation: heroHeadlineReveal 1.05s cubic-bezier(0.16, 1, 0.3, 1) 0.18s both;
      will-change: transform, opacity, filter;
    }

    .hero-sub-text {
      animation: heroTextReveal 0.95s cubic-bezier(0.16, 1, 0.3, 1) 0.30s both;
      will-change: transform, opacity, filter;
    }

    .hero-cta-group {
      animation: heroBtnSpring 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.44s both;
    }

    /* Hero Search Card Docking Entrance */
    #lookup-card {
      position: relative;
      overflow: hidden;
      animation: cinematicCardDock 1.15s cubic-bezier(0.16, 1, 0.3, 1) 0.22s both;
      will-change: transform, opacity, filter;
    }

    #lookup-card::after {
      content: "";
      position: absolute;
      top: 0;
      left: -140%;
      width: 70%;
      height: 100%;
      background: linear-gradient(
        90deg,
        transparent 0%,
        rgba(255, 255, 255, 0.15) 20%,
        rgba(255, 255, 255, 0.6) 50%,
        rgba(108, 181, 255, 0.4) 75%,
        transparent 100%
      );
      transform: skewX(-24deg);
      pointer-events: none;
      z-index: 30;
      animation: cardSheenSweep 1.8s cubic-bezier(0.16, 1, 0.3, 1) 0.4s both;
    }

    /* === WHOLE-PAGE MULTI-DIRECTIONAL CINEMATIC SCROLL REVEAL SYSTEM === */
    .reveal-ready {
      opacity: 0 !important;
      will-change: transform, opacity, filter;
      transition: opacity 0.88s cubic-bezier(0.16, 1, 0.3, 1),
                  transform 0.88s cubic-bezier(0.16, 1, 0.3, 1),
                  filter 0.88s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    /* Direction 1: Upward Lift & Focal De-blur */
    .reveal-ready.reveal-up {
      transform: translateY(42px) scale(0.97) !important;
      filter: blur(8px) !important;
    }

    /* Direction 2: Left Visual / 3D Art Entrance */
    .reveal-ready.reveal-left {
      transform: translateX(-48px) scale(0.96) !important;
      filter: blur(10px) !important;
    }

    /* Direction 3: Right Narrative / Content Card Entrance */
    .reveal-ready.reveal-right {
      transform: translateX(48px) scale(0.96) !important;
      filter: blur(10px) !important;
    }

    /* Direction 4: Scale Pop Entrance for Feature & Tool Cards */
    .reveal-ready.reveal-scale {
      transform: translateY(28px) scale(0.93) !important;
      filter: blur(8px) !important;
    }

    /* Resting State: Full Reset for Perfect Layout Stability */
    .reveal-ready.is-visible {
      opacity: 1 !important;
      transform: none !important;
      filter: none !important;
    }

    /* Interactive Cursor Spotlight on Cards */
    .glass-white,
    .glass-navy,
    .faq-accordion-item,
    .step-pipeline-item {
      position: relative;
      overflow: hidden;
      transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.32s ease, border-color 0.32s ease !important;
    }

    .glass-white::before,
    .glass-navy::before,
    .faq-accordion-item::before,
    .step-pipeline-item::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      background: radial-gradient(
        380px circle at var(--spot-x, -999px) var(--spot-y, -999px),
        rgba(108, 181, 255, 0.18),
        transparent 70%
      );
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.35s ease;
      z-index: 10;
    }

    .glass-white:hover::before,
    .glass-navy:hover::before,
    .faq-accordion-item:hover::before,
    .step-pipeline-item:hover::before {
      opacity: 1;
    }

    @media (prefers-reduced-motion: reduce) {
      .hero-pill-badge,
      .hero-main-title,
      .hero-sub-text,
      .hero-cta-group,
      #lookup-card,
      #lookup-card::after,
      .reveal-ready,
      .reveal-ready.reveal-up,
      .reveal-ready.reveal-left,
      .reveal-ready.reveal-right,
      .reveal-ready.reveal-scale {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
        filter: none !important;
        transition: none !important;
      }
    }

    .extractmail-faq-content {
      transition: max-height 0.35s ease;
    }

  </style>


<main class="extractmail-page min-h-screen bg-brand-navy text-brand-textDarkMain font-sans antialiased selection:bg-brand-blue selection:text-white">
  <section id="hero-section" class="relative bg-brand-navy min-h-screen pt-4 pb-24 md:pb-32 overflow-hidden border-b border-brand-navySec/40"><!-- Ambient Multi-Layered Tech Atmosphere & Glows -->
    <div class="absolute inset-0 pointer-events-none -z-0 overflow-hidden">
      <div class="absolute -top-40 -left-40 w-[640px] h-[640px] bg-blue-600/20 rounded-full blur-[130px]"></div>
      <div class="absolute top-1/4 right-0 w-[720px] h-[720px] bg-gradient-to-br from-blue-500/25 via-indigo-600/15 to-transparent rounded-full blur-[140px]"></div>
      <div class="absolute -bottom-20 left-1/3 w-[520px] h-[520px] bg-cyan-500/10 rounded-full blur-[120px]"></div>
      <!-- Tech Grid Texture with radial mask -->
      <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(rgba(255,255,255,0.7) 1px, transparent 1px); background-size: 28px 28px;"></div>
      <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-brand-navy"></div>
    </div>

    <div class="max-w-[1240px] mx-auto px-5 sm:px-6 relative z-10">
      


      <!-- Breadcrumb -->
      <nav aria-label="Breadcrumb" class="mb-6 md:mb-10">
        <ol class="flex items-center space-x-2 text-xs md:text-sm text-brand-textDarkSec font-medium">
          <li class="">
            <a href="#" class="hover:text-white transition-colors">Home</a>
          </li>
          <li class="flex items-center space-x-2">
            <span class="text-brand-textDarkSec/60">/</span>
            <a href="#other-tools" class="hover:text-white transition-colors">Tools</a>
          </li>
          <li class="flex items-center space-x-2">
            <span class="text-brand-textDarkSec/60">/</span>
            <span class="text-brand-blueHighlight font-semibold" aria-current="page">Reverse Email Lookup Tool</span>
          </li>
        </ol>
      </nav>

      <!-- Two-Column Hero Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center pt-2 pb-6">
        
        <!-- Left Column: Copy & Actions -->
        <div class="lg:col-span-6 space-y-6 md:space-y-7">
          <!-- Intelligence Engine Pill -->
          <div class="hero-pill-badge inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-blue-500/10 border border-blue-400/30 text-xs md:text-sm font-medium text-blue-200 backdrop-blur-md shadow-inner shadow-blue-500/10">
            <span class="relative flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-blueHighlight opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-blueHighlight"></span>
            </span>
            <span class="">Apollo-powered Intelligence Engine</span>
            <span class="text-white/30 hidden sm:inline">•</span>
            <span class="text-blue-300/80 font-mono text-[11px] hidden sm:inline">v4.2</span>
          </div>

          <!-- Catchy Modern Typography Headline -->
          <h1 class="hero-main-title text-4xl sm:text-5xl lg:text-[62px] font-extrabold text-white leading-[1.08] tracking-tight">
            Reverse Email <br class="hidden sm:inline">
            <span class="bg-gradient-to-r from-blue-400 via-sky-300 to-indigo-200 bg-clip-text text-transparent drop-shadow-[0_4px_18px_rgba(59,130,246,0.35)]">Lookup Tool</span>
          </h1>

          <!-- Supporting Body -->
          <p class="hero-sub-text text-base sm:text-lg md:text-[18px] text-brand-textDarkSec/90 leading-relaxed max-w-xl font-normal">
            Convert any professional email into actionable insight with our Apollo-powered Reverse Email Lookup Tool. Discover names, companies, and public profile details in a clean, fast workflow.
          </p>

          <!-- Primary CTA & Modern Trust Badges -->
          <div class="hero-cta-group space-y-4 pt-1">
            <div class="flex flex-wrap items-center gap-4">
              <button id="tryNowBtn" class="bg-gradient-to-r from-brand-blue to-indigo-600 hover:from-blue-600 hover:to-indigo-500 text-white font-semibold text-base px-8 py-3.5 rounded-full shadow-xl shadow-brand-blue/35 hover:shadow-brand-blue/55 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2.5 border border-blue-400/30">
                <span class="">Try Now</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
              </button>
              <div class="flex items-center gap-2 text-xs text-brand-textDarkSec/80 font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="">Zero credit card required for preview</span>
              </div>
            </div>

            <!-- Upgraded Trust Micro-Badges -->
            <div class="flex flex-wrap items-center gap-3 pt-2">
              <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/[0.06] border border-white/10 text-xs text-slate-200 backdrop-blur-md">
                <span class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                  </svg>
                </span>
                <span class="">Instant enrichments</span>
              </div>
              <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/[0.06] border border-white/10 text-xs text-slate-200 backdrop-blur-md">
                <span class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                  </svg>
                </span>
                <span class="">Verified corporate data</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Prominent Lookup Form + Seamless 3D Identity Sculpture -->
        <div class="lg:col-span-6 relative pt-4 lg:pt-0">
          
          <!-- Glowing Halo & Background 3D Identity-Reveal Sculpture (IMAGE_5) -->
         

          <!-- Floating Accent Glass Chip -->
          <div class="hidden sm:flex absolute -top-4 -left-6 z-30 glass-navy px-3.5 py-1.5 rounded-full border border-blue-400/40 text-[11px] text-blue-200 font-mono shadow-xl items-center gap-2 backdrop-blur-md">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
            <span class="">ReverseDNS • Live Graph</span>
          </div>

          <!-- Prominent Glass Search Card with Refined Glassmorphism -->
          <div id="lookup-card" class="bg-white/95 backdrop-blur-2xl text-brand-textLightMain rounded-[28px] p-6 sm:p-8 md:p-9 relative z-20 shadow-[0_25px_60px_-15px_rgba(7,28,80,0.45),0_0_40px_rgba(37,99,235,0.22)] border border-white/80 transition-all duration-300">
            
            <div class="flex items-center justify-between mb-4">
              <span class="inline-flex items-center gap-1.5 text-[11px] font-bold tracking-wider text-brand-blue uppercase bg-blue-50/80 px-3 py-1 rounded-full border border-blue-200/60 shadow-sm">
                <svg class="w-3 h-3 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                EMAIL LOOKUP
              </span>
              <span class="text-xs font-semibold text-slate-600 flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-full border border-slate-200/60">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Node Ready
              </span>
            </div>

            <h2 class="text-2xl sm:text-[28px] font-bold text-brand-navy leading-snug mb-2 tracking-tight">
              Who's behind this email address?
            </h2>
            
            <p class="text-xs sm:text-sm text-brand-textLightSec mb-6 leading-relaxed">
              Enter any corporate email to uncover executive name, role, organization, and social profile links.
            </p>

            <!-- Search Form -->
            <form id="emailLookupForm" class="space-y-4" onsubmit="event.preventDefault(); handleLookupSubmit();">
              <div>
                <label for="emailInput" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                  Professional Email Address <span class="text-red-500">*</span>
                </label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-blue transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path>
                    </svg>
                  </div>
                  <input type="email" id="emailInput" required="" placeholder="name@company.com" class="w-full pl-11 pr-32 py-3.5 bg-white border-2 border-brand-borderLight rounded-xl text-base font-medium text-brand-textLightMain placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:ring-4 focus:ring-brand-blue/15 transition-all shadow-inner">
                  <div class="absolute inset-y-1.5 right-1.5 flex items-center">
                    <button type="submit" id="searchBtn" class="h-full px-5 bg-gradient-to-r from-brand-blue to-indigo-600 hover:from-blue-600 hover:to-indigo-500 active:scale-95 text-white text-sm font-semibold rounded-lg shadow-md shadow-brand-blue/30 transition-all flex items-center justify-center gap-1.5 border border-blue-400/20">
                      <span id="btnText" class="">Search</span>
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                      </svg>
                      <svg id="btnSpinner" class="hidden w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                
                <!-- Feedback / Status message container -->
                <div id="lookupMessage" class="mt-2 text-xs flex items-center gap-1.5 hidden"></div>
              </div>

              <div class="flex items-center justify-between text-xs text-brand-textLightSec pt-1 border-t border-slate-100">
                <span class="flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-brand-blue" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                  </svg>
                  Helper note:
                </span>
                <span class="font-medium text-brand-navy">Best for professional email addresses.</span>
              </div>
            </form>

            <!-- Interactive Demo Result Box (Active Preview) -->
            <div id="demoResultBox" class="mt-5 p-4 rounded-2xl bg-gradient-to-b from-brand-icy to-white border border-brand-borderLight text-brand-textLightMain transition-all shadow-sm">
              <div class="flex items-center justify-between pb-2 mb-3 border-b border-brand-borderLight/80">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-brand-blue uppercase bg-white px-2.5 py-0.5 rounded-full border border-brand-borderLight shadow-sm">
                  <span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span> Demo result
                </span>
                <span class="text-[11px] font-mono text-brand-textLightSec">Query: sarah.jenkins@acmecorp.io</span>
              </div>
              <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-brand-navy to-brand-navySec flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-md ring-2 ring-blue-500/20">
                  SJ
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-2">
                    <h4 class="text-sm font-bold text-brand-navy truncate">Sarah Jenkins</h4>
                    <span class="text-[11px] bg-emerald-50 text-emerald-700 font-semibold px-2 py-0.5 rounded-full border border-emerald-200 shrink-0 flex items-center gap-1">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified 99.8%
                    </span>
                  </div>
                  <p class="text-xs font-semibold text-brand-blue truncate">VP of Strategic Partnerships</p>
                  <p class="text-xs text-brand-textLightSec truncate">Acme Global Technologies • San Francisco, CA</p>
                  
                  <div class="mt-2.5 pt-2 border-t border-slate-200/60 flex flex-wrap items-center gap-2.5 text-[11px] text-brand-textLightSec">
                    <span class="inline-flex items-center gap-1 text-slate-700 font-medium bg-slate-100/80 px-2 py-0.5 rounded border border-slate-200/70 hover:text-brand-blue transition-colors cursor-pointer">
                      <svg class="w-3.5 h-3.5 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
                      LinkedIn Public Profile
                    </span>
                    <span class="inline-flex items-center text-slate-600 font-mono text-[10px] bg-slate-100/80 px-2 py-0.5 rounded border border-slate-200/70">
                      Corp Domain: acmecorp.io
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pre-set Demo Switchers -->
            <div class="mt-3.5 flex items-center justify-between text-[11px] text-brand-textLightSec flex-wrap gap-2">
              <span class="font-medium text-slate-500">Try test query:</span>
              <div class="flex items-center gap-1.5">
                <button type="button" onclick="setDemoQuery('david.choi@finscale.ai', 'valid')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-brand-pale text-slate-700 hover:text-brand-blue font-mono transition-colors border border-slate-200/60">david.choi@finscale.ai</button>
                <span class="text-slate-300">•</span>
                <button type="button" onclick="setDemoQuery('unregistered@testmail.xyz', 'nomatch')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-brand-pale text-slate-700 hover:text-brand-blue font-mono transition-colors border border-slate-200/60">unregistered@testmail.xyz</button>
              </div>
            </div>

          </div>
        </div>

      </div>

    </div></section>

  <!-- =========================================================================
       SECTION 2: LIGHT INTRODUCTORY VALUE SECTION
       Theme: Light White (#FFFFFF)
       ========================================================================= -->
  <section id="section-intro" class="py-24 md:py-32 bg-white text-brand-textLightMain relative overflow-hidden">
    <div class="max-w-[1200px] mx-auto px-5 sm:px-6">
      
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
        
        <!-- Left: Editorial Text Content -->
        <div class="lg:col-span-6 space-y-6 intro-content">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-pale text-brand-blue text-xs font-bold uppercase tracking-wider border border-brand-borderLight">
            Executive Intelligence
          </div>

          <h2 class="text-3xl sm:text-4xl md:text-[42px] font-bold text-brand-navy leading-tight tracking-tight">
            Find Names, Profiles &amp; Details from Any Email Address
          </h2>

          <p class="text-base sm:text-lg text-brand-textLightSec leading-relaxed">
            ExtractMails Reverse Email Lookup empowers sales, recruitment, and security professionals to instantly identify the individual and corporate organization behind any inbound or outbound email contact. By cross-referencing global corporate data registries, Apollo-indexed company repositories, and publicly indexed professional networks, you uncover full names, current job titles, corporate hierarchy, and verified business domains in milliseconds.
          </p>

          <div class="pt-4 grid grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-brand-icy border border-brand-borderLight">
              <div class="text-2xl font-extrabold text-brand-navy mb-1">0.4s</div>
              <p class="text-xs font-medium text-brand-textLightSec">Average enrichment response latency</p>
            </div>
            <div class="p-4 rounded-2xl bg-brand-icy border border-brand-borderLight">
              <div class="text-2xl font-extrabold text-brand-blue mb-1">320M+</div>
              <p class="text-xs font-medium text-brand-textLightSec">Verified B2B profile records</p>
            </div>
          </div>
        </div>

        <!-- Right: Ultra-Realistic Photography + Floating Glass Contact Preview -->
        <div class="lg:col-span-6 relative intro-visual">
          <div class="relative rounded-[26px] overflow-hidden shadow-2xl border-4 border-brand-borderLight group">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBKRILONyN7K4mikze26z8gfB1WexgXN9LZ4Jg7z1zDsTHQie54k3oIY3qs9AOu4yBEnZNyZ6hbzScu7ecPRjnRwwGpCJHnUKm0IUsjkBft1_G2F8cw1pMczRpauUq_aDP3lTKqRyEWtWLIHASZ_8GgqxLZDGjrDY0__k1XZQzPqnM6Twx9OSNKvP8a1wHea6RZU6RXp0oAseP8xe550zai23IpH_6CAUSZde0rF8-Un2sAFYC36TVo" alt="Professional business researcher reviewing verified contact records in a modern workspace" class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
            <!-- Gradient Overlay for Contrast -->
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy/60 via-transparent to-transparent"></div>
          </div>

          <!-- Separate Floating Glass Contact Preview Overlay -->
          <div class="absolute -bottom-8 -left-4 sm:-bottom-10 sm:-left-6 max-w-xs sm:max-w-sm glass-white rounded-2xl p-4 sm:p-5 shadow-2xl border border-brand-borderLight">
            <div class="flex items-center justify-between mb-2">
              <span class="text-[10px] font-bold tracking-wider uppercase text-brand-blue bg-brand-pale px-2 py-0.5 rounded">
                Example Preview
              </span>
              <span class="text-[11px] font-mono text-emerald-600 font-semibold">Status: 200 OK</span>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-brand-blue to-brand-blueHighlight flex items-center justify-center text-white font-bold text-xs shadow-md">
                EK
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-brand-navy truncate">Elena Rostova</p>
                <p class="text-[11px] text-brand-textLightSec truncate">Lead Data Scientist • TechSphere Corp</p>
                <p class="text-[10px] font-mono text-brand-blue truncate">elena.r@techsphere.com</p>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>
  </section>

  <!-- =========================================================================
       SECTION 3: DARK TOOL DEFINITION
       Theme: Dark Navy (#071C50)
       ========================================================================= -->
  <section id="section-definition" class="py-24 md:py-32 bg-brand-navy text-white relative overflow-hidden border-y border-brand-navySec/50">
    <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-[700px] h-[700px] glow-lens pointer-events-none -z-0"></div>

    <div class="max-w-[1200px] mx-auto px-5 sm:px-6 relative z-10">
      
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
        
        <!-- Left: Compact 3D Lens-and-Domain Sculpture Asset -->
        <div class="lg:col-span-5 order-2 lg:order-1 flex justify-center definition-visual">
          <div class="relative w-72 h-72 sm:w-88 sm:h-88 md:w-96 md:h-96">
            <div class="absolute inset-0 bg-brand-blue/20 rounded-full blur-3xl pointer-events-none"></div>
            <img src="https://www.extractmails.com/wp-content/uploads/2024/12/eyeboxreverse.png" alt="3D Lens and Domain inspection sculpture" class="w-full h-full object-contain filter drop-shadow-[0_25px_45px_rgba(37,99,235,0.4)] relative z-10 transform hover:rotate-2 hover:scale-105 transition-all duration-500">
            <div class="absolute -bottom-4 right-4 glass-navy rounded-xl px-3.5 py-1.5 border border-brand-blueHighlight/40 text-[11px] text-brand-textDarkSec font-mono">
              Domain Node // Inspection Mode
            </div>
          </div>
        </div>

        <!-- Right: Text & Definition Heading -->
        <div class="lg:col-span-7 order-1 lg:order-2 space-y-6 definition-content">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navySec/80 border border-brand-blueHighlight/30 text-xs font-medium text-brand-blueHighlight">
            Technical Architecture
          </div>

          <h2 class="text-3xl sm:text-4xl md:text-[42px] font-bold text-white tracking-tight leading-tight">
            What is Reverse Email Lookup Tool
          </h2>

          <p class="text-base sm:text-lg text-brand-textDarkSec leading-relaxed font-normal">
            A Reverse Email Lookup Tool is an automated intelligence solution designed to extract and assemble verifiable identity data from a single input email address. Rather than searching manually through countless web directories and corporate portals, the tool aggregates digital footprints, company registry associations, domain DNS ownership, and authenticated social graph metadata. It translates anonymous string handles into enriched business profiles, including full names, executive titles, organization metrics, and verified online presences.
          </p>

          <div class="pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="glass-navy-subtle p-4 rounded-xl flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-brand-blue/30 text-brand-blueHighlight flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-white">Automated Cross-Referencing</h3>
                <p class="text-xs text-brand-textDarkSec mt-0.5">Scans live domain registries, social APIs, and data graphs.</p>
              </div>
            </div>

            <div class="glass-navy-subtle p-4 rounded-xl flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-brand-blue/30 text-brand-blueHighlight flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-white">Synthesized Insight</h3>
                <p class="text-xs text-brand-textDarkSec mt-0.5">Outputs human-readable executive context without manual hunting.</p>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- =========================================================================
       SECTION 4: LIGHT KEY FEATURES
       Theme: Light Icy Blue (#F6FAFD)
       ========================================================================= -->
  <section id="features" class="py-24 md:py-32 bg-brand-icy text-brand-textLightMain relative overflow-hidden">
    <div class="max-w-[1200px] mx-auto px-5 sm:px-6">
      
      <!-- Section Header -->
      <div class="section-header text-center max-w-3xl mx-auto mb-16 space-y-4">
        <span class="inline-block text-xs font-bold tracking-widest text-brand-blue uppercase bg-brand-pale px-3 py-1 rounded-full border border-brand-borderLight">
          Core Capabilities
        </span>
        <h2 class="text-3xl sm:text-4xl md:text-[42px] font-bold text-brand-navy tracking-tight">
          Key Features of Reverse Email Lookup Tool
        </h2>
        <p class="text-base sm:text-lg text-brand-textLightSec leading-relaxed">
          Engineered to give sales development reps, investigators, and recruiters instant access to deep contextual intelligence from every address query.
        </p>
      </div>

      <!-- 4 Refined White Glass Feature Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Feature 1: Instant Email Lookup -->
        <div class="feature-card glass-white rounded-[22px] p-8 hover:-translate-y-1.5 hover:shadow-xl transition-all duration-300 border border-brand-borderLight flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-xl bg-brand-pale text-brand-blue flex items-center justify-center mb-6 shadow-sm">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-brand-navy mb-3 tracking-tight">
              Instant Email Lookup
            </h3>
            <p class="text-base text-brand-textLightSec leading-relaxed">
              Execute lightning-fast real-time queries that scan through millions of active professional database entries in seconds. Eliminate manual investigation delays with zero latency lookups.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-brand-borderLight flex items-center text-xs font-semibold text-brand-blue">
            <span class="">Query speed &lt; 500ms</span>
          </div>
        </div>

        <!-- Feature 2: Identity Discovery -->
        <div class="feature-card glass-white rounded-[22px] p-8 hover:-translate-y-1.5 hover:shadow-xl transition-all duration-300 border border-brand-borderLight flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-xl bg-brand-pale text-brand-blue flex items-center justify-center mb-6 shadow-sm">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-brand-navy mb-3 tracking-tight">
              Identity Discovery
            </h3>
            <p class="text-base text-brand-textLightSec leading-relaxed">
              Reveal verified full first and last names, aliases, and authenticated personal identifiers connected directly to the searched email address across business networks.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-brand-borderLight flex items-center text-xs font-semibold text-brand-blue">
            <span class="">First &amp; last name verification</span>
          </div>
        </div>

        <!-- Feature 3: Professional & Company Insights -->
        <div class="feature-card glass-white rounded-[22px] p-8 hover:-translate-y-1.5 hover:shadow-xl transition-all duration-300 border border-brand-borderLight flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-xl bg-brand-pale text-brand-blue flex items-center justify-center mb-6 shadow-sm">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-brand-navy mb-3 tracking-tight">
              Professional &amp; Company Insights
            </h3>
            <p class="text-base text-brand-textLightSec leading-relaxed">
              Unpack vital business intelligence including job title, department, company name, corporate headquarters, company size, and associated industrial domain metrics.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-brand-borderLight flex items-center text-xs font-semibold text-brand-blue">
            <span class="">Full firmographic context</span>
          </div>
        </div>

        <!-- Feature 4: Social & Online Profiles -->
        <div class="feature-card glass-white rounded-[22px] p-8 hover:-translate-y-1.5 hover:shadow-xl transition-all duration-300 border border-brand-borderLight flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-xl bg-brand-pale text-brand-blue flex items-center justify-center mb-6 shadow-sm">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
              </svg>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-brand-navy mb-3 tracking-tight">
              Social &amp; Online Profiles
            </h3>
            <p class="text-base text-brand-textLightSec leading-relaxed">
              Link the email directly to publicly accessible online presences, including verified LinkedIn profiles, professional social feeds, and verified web portfolios.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-brand-borderLight flex items-center text-xs font-semibold text-brand-blue">
            <span class="">Public cross-platform linking</span>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- =========================================================================
       SECTION 5: DARK USE CASES
       Theme: Dark Navy (#071C50)
       ========================================================================= -->
  <section id="use-cases" class="py-24 md:py-32 bg-brand-navy text-white relative overflow-hidden border-t border-brand-navySec/40">
    <!-- Localized Blue Backlight -->
    <div class="absolute bottom-0 right-1/4 w-[600px] h-[600px] bg-brand-blue/15 rounded-full blur-3xl pointer-events-none -z-0"></div>

    <div class="max-w-[1200px] mx-auto px-5 sm:px-6 relative z-10">
      
      <!-- Section Header -->
      <div class="section-header text-center max-w-3xl mx-auto mb-16 space-y-4">
        <span class="inline-block text-xs font-bold tracking-widest text-brand-blueHighlight uppercase bg-brand-navySec/80 px-3.5 py-1 rounded-full border border-brand-blueHighlight/30">
          Practical Applications
        </span>
        <h2 class="text-3xl sm:text-4xl md:text-[42px] font-bold text-white tracking-tight">
          Why Use Reverse Email Lookup Tool
        </h2>
        <p class="text-base sm:text-lg text-brand-textDarkSec leading-relaxed">
          Unlock actionable verification, mitigate business risks, and supercharge sales workflows across all your organizational communications.
        </p>
      </div>

      <!-- 4 Substantial Editorial Panels -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Panel 1: Identify Unknown Senders -->
        <div class="use-case-card glass-navy rounded-[24px] p-8 sm:p-9 border border-brand-blueHighlight/20 flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <span class="w-8 h-8 rounded-lg bg-brand-blue/30 text-brand-blueHighlight flex items-center justify-center font-bold text-sm">
                01
              </span>
              <h3 class="text-2xl font-bold text-white tracking-tight">Identify Unknown Senders</h3>
            </div>
            
            <p class="text-base text-brand-textDarkSec leading-relaxed mb-4">
              Unmask unidentified inbound inquiries, partnership requests, or ambiguous correspondence immediately. Gain full transparency before committing time to conversations.
            </p>

            <p class="text-sm font-semibold text-brand-pale mb-3">Key investigative advantages:</p>
            <ul class="space-y-2.5 text-sm text-brand-textDarkSec">
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Ascertain the genuine person and organization sending cold pitches or critical inquiries.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Determine sender legitimacy and authority level within their stated company hierarchy.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Prioritize urgent executive communications while ignoring unsolicited noise.</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Panel 2: Verify Leads & Prospects -->
        <div class="use-case-card glass-navy rounded-[24px] p-8 sm:p-9 border border-brand-blueHighlight/20 flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <span class="w-8 h-8 rounded-lg bg-brand-blue/30 text-brand-blueHighlight flex items-center justify-center font-bold text-sm">
                02
              </span>
              <h3 class="text-2xl font-bold text-white tracking-tight">Verify Leads &amp; Prospects</h3>
            </div>
            
            <p class="text-base text-brand-textDarkSec leading-relaxed mb-4">
              Ensure sales reps engage with qualified decision-makers instead of low-intent generic inboxes. Validate prospect credibility before scheduling demos.
            </p>

            <p class="text-sm font-semibold text-brand-pale mb-3">Key qualification advantages:</p>
            <ul class="space-y-2.5 text-sm text-brand-textDarkSec">
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Confirm active employment status and executive title for inbound web leads.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Target personalized sales messaging tailored to the prospect’s exact seniority level.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Eliminate bounce rates by verifying corporate domain vitality before launching campaigns.</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Panel 3: Avoid Scams & Fraud -->
        <div class="use-case-card glass-navy rounded-[24px] p-8 sm:p-9 border border-brand-blueHighlight/20 flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <span class="w-8 h-8 rounded-lg bg-brand-blue/30 text-brand-blueHighlight flex items-center justify-center font-bold text-sm">
                03
              </span>
              <h3 class="text-2xl font-bold text-white tracking-tight">Avoid Scams &amp; Fraud</h3>
            </div>
            
            <p class="text-base text-brand-textDarkSec leading-relaxed mb-4">
              Defend your corporate perimeter against deceptive spoofing, spoofed invoices, business email compromise (BEC), and phishing vectors by verifying identities against authoritative registries.
            </p>

            <p class="text-sm font-semibold text-brand-pale mb-3">Key fraud prevention advantages:</p>
            <ul class="space-y-2.5 text-sm text-brand-textDarkSec">
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Detect lookalike domain variants impersonating internal leadership or external vendors.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Unmask malicious throwaway webmail accounts posing as legitimate enterprise buyers.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Safeguard accounts payable from fraudulent wire transfer update requests.</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Panel 4: Enrich Your CRM Data (Featuring Sales Ops Photography) -->
        <div class="use-case-card glass-navy rounded-[24px] p-8 sm:p-9 border border-brand-blueHighlight/20 flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <span class="w-8 h-8 rounded-lg bg-brand-blue/30 text-brand-blueHighlight flex items-center justify-center font-bold text-sm">
                04
              </span>
              <h3 class="text-2xl font-bold text-white tracking-tight">Enrich Your CRM Data</h3>
            </div>
            
            <p class="text-base text-brand-textDarkSec leading-relaxed mb-4">
              Transform skeletal email lists into fully populated, high-converting CRM records. Automatically append verified names, job designations, corporate websites, and social coordinates.
            </p>

            <p class="text-sm font-semibold text-brand-pale mb-3">Key CRM enrichment advantages:</p>
            <ul class="space-y-2.5 text-sm text-brand-textDarkSec mb-6">
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Fill missing database columns with verified firmographic and demographic data.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Enable precision lead scoring and segment audience rosters by company size and tier.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-blueHighlight mt-2 shrink-0"></span>
                <span class="">Maintain spotless contact health across Salesforce, HubSpot, and custom pipelines.</span>
              </li>
            </ul>

            <!-- Photographic Supporting Asset -->
            <div class="relative rounded-xl overflow-hidden border border-brand-blueHighlight/30 shadow-lg">
              <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDJYupTo28ydKQ_aW-_1pdIYvhFlq26fvvo-8yBcP1CC203wt3f4FjoThwwGCY9IS4Bb7OIx8o8-GZ5xf1sXKmoXhSRaOuXFp3cshrP3Coy3keOu3ShKjFiYdsypxVWNpVJZrdbsHq_ZIpSTrCoNYbGKEt8TdcgvpVBBeGCiK-3igPkFkYaGIJwg1O6ZKRS63AddkSZaaf39JRe2EWBVSd3uwEsu3mEW6yvGI_hJ89z2rzPe2-0nkxH" alt="Sales operations team reviewing CRM data enrichment analytics on a modern shared studio display" class="w-full h-48 object-cover">
              <div class="absolute bottom-2 left-3 bg-brand-navy/85 px-3 py-1 rounded text-[11px] text-white font-mono border border-white/20">
                Live CRM Sync Telemetry
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- =========================================================================
       SECTION 6: LIGHT PROCESS SECTION
       Theme: Light White (#FFFFFF)
       ========================================================================= -->
  <section id="how-it-works" class="py-24 md:py-32 bg-white text-brand-textLightMain relative overflow-hidden">
    <div class="max-w-[1200px] mx-auto px-5 sm:px-6">
      
      <!-- Section Header -->
      <div class="section-header max-w-3xl mb-16 space-y-4">
        <span class="inline-block text-xs font-bold tracking-widest text-brand-blue uppercase bg-brand-pale px-3 py-1 rounded-full border border-brand-borderLight">
          Operational Flow
        </span>
        <h2 class="text-3xl sm:text-4xl md:text-[42px] font-bold text-brand-navy tracking-tight">
          How Reverse Email Lookup Tool Works?
        </h2>
        <p class="text-base sm:text-lg text-brand-textLightSec leading-relaxed">
          The process transforms an input email into deep context in a fast, automated pipeline. Here's a closer look at how the process works:
        </p>
      </div>

      <!-- 6 Numbered Positions with Step 1 Active -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        
        <!-- Step 1: Active Stage (Detailed Content) -->
        <div class="step-active-card md:col-span-7 glass-white rounded-[24px] p-8 sm:p-9 border-2 border-brand-blue shadow-xl relative overflow-hidden">
          <div class="flex items-center justify-between mb-6">
            <span class="w-12 h-12 rounded-xl bg-brand-blue text-white flex items-center justify-center font-bold text-xl shadow-md">
              01
            </span>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-blue uppercase bg-brand-pale px-3 py-1 rounded-full border border-brand-borderLight">
              <span class="w-2 h-2 rounded-full bg-brand-blue animate-pulse"></span> Active Stage
            </span>
          </div>

          <h3 class="text-2xl sm:text-3xl font-bold text-brand-navy mb-4 tracking-tight">
            Enter the Email Address
          </h3>

          <p class="text-base sm:text-lg text-brand-textLightSec leading-relaxed mb-6">
            The process starts by entering the email address you want to investigate into the search bar.
          </p>

          <!-- Refined Email Input Illustration -->
          <div class="p-4 rounded-xl bg-brand-icy border border-brand-borderLight space-y-3">
            <div class="text-xs font-semibold text-brand-textLightSec flex items-center justify-between">
              <span class="">Investigation Query Input</span>
              <span class="text-emerald-600 font-mono text-[11px]">Format: RFC 5322 Compliant</span>
            </div>
            <div class="flex items-center gap-2 bg-white p-2.5 rounded-lg border border-brand-borderLight shadow-inner">
              <div class="w-7 h-7 rounded bg-brand-pale text-brand-blue flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              </div>
              <span class="text-sm font-mono text-brand-navy font-semibold truncate">alex.morgan@vanguardscale.tech</span>
              <span class="ml-auto w-2 h-4 bg-brand-blue animate-pulse"></span>
            </div>
            <p class="text-[11px] text-brand-textLightSec">Single query intake passes into the Apollo-indexed data validation pipeline.</p>
          </div>
        </div>

        <!-- Steps 2 to 6: Numbered Indicators Pipeline -->
        <div class="md:col-span-5 space-y-4">
          
          <!-- Step 2 -->
          <div class="step-pipeline-item glass-white p-5 rounded-2xl border border-brand-borderLight/80 flex items-center justify-between opacity-85">
            <div class="flex items-center gap-4">
              <span class="w-9 h-9 rounded-lg bg-brand-pale text-brand-navy font-bold text-sm flex items-center justify-center">
                02
              </span>
              <span class="text-sm font-semibold text-brand-navy">Pipeline Stage 2</span>
            </div>
            <span class="text-xs font-mono text-brand-textLightSec">Queue position 02</span>
          </div>

          <!-- Step 3 -->
          <div class="step-pipeline-item glass-white p-5 rounded-2xl border border-brand-borderLight/80 flex items-center justify-between opacity-75">
            <div class="flex items-center gap-4">
              <span class="w-9 h-9 rounded-lg bg-brand-pale text-brand-navy font-bold text-sm flex items-center justify-center">
                03
              </span>
              <span class="text-sm font-semibold text-brand-navy">Pipeline Stage 3</span>
            </div>
            <span class="text-xs font-mono text-brand-textLightSec">Queue position 03</span>
          </div>

          <!-- Step 4 -->
          <div class="step-pipeline-item glass-white p-5 rounded-2xl border border-brand-borderLight/80 flex items-center justify-between opacity-65">
            <div class="flex items-center gap-4">
              <span class="w-9 h-9 rounded-lg bg-brand-pale text-brand-navy font-bold text-sm flex items-center justify-center">
                04
              </span>
              <span class="text-sm font-semibold text-brand-navy">Pipeline Stage 4</span>
            </div>
            <span class="text-xs font-mono text-brand-textLightSec">Queue position 04</span>
          </div>

          <!-- Step 5 -->
          <div class="step-pipeline-item glass-white p-5 rounded-2xl border border-brand-borderLight/80 flex items-center justify-between opacity-55">
            <div class="flex items-center gap-4">
              <span class="w-9 h-9 rounded-lg bg-brand-pale text-brand-navy font-bold text-sm flex items-center justify-center">
                05
              </span>
              <span class="text-sm font-semibold text-brand-navy">Pipeline Stage 5</span>
            </div>
            <span class="text-xs font-mono text-brand-textLightSec">Queue position 05</span>
          </div>

          <!-- Step 6 -->
          <div class="step-pipeline-item glass-white p-5 rounded-2xl border border-brand-borderLight/80 flex items-center justify-between opacity-50">
            <div class="flex items-center gap-4">
              <span class="w-9 h-9 rounded-lg bg-brand-pale text-brand-navy font-bold text-sm flex items-center justify-center">
                06
              </span>
              <span class="text-sm font-semibold text-brand-navy">Pipeline Stage 6</span>
            </div>
            <span class="text-xs font-mono text-brand-textLightSec">Queue position 06</span>
          </div>

        </div>

      </div>

    </div>
  </section>

  <!-- =========================================================================
       SECTION 7: DARK RELATED TOOLS
       Theme: Dark Navy (#071C50)
       ========================================================================= -->
  <section id="other-tools" class="py-24 md:py-32 bg-brand-navy text-white relative overflow-hidden border-t border-brand-navySec/40">
    <div class="max-w-[1200px] mx-auto px-5 sm:px-6 relative z-10">
      
      <!-- Section Header -->
      <div class="section-header flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
        <div>
          <span class="inline-block text-xs font-bold tracking-widest text-brand-blueHighlight uppercase bg-brand-navySec/80 px-3.5 py-1 rounded-full border border-brand-blueHighlight/30 mb-3">
            Suite Ecosystem
          </span>
          <h2 class="text-3xl sm:text-4xl md:text-[42px] font-bold text-white tracking-tight">
            Other Tools
          </h2>
        </div>
        <p class="text-base text-brand-textDarkSec max-w-md">
          Explore complementary email automation, validation, and deliverability solutions from ExtractMails.
        </p>
      </div>

      <!-- 4 Linked Tool Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Tool 1: Email Spam Checker -->
        <a href="#hero-section" class="other-tool-card glass-navy rounded-[22px] p-6 hover:-translate-y-2 hover:border-brand-blueHighlight transition-all duration-300 flex flex-col justify-between group border border-white/10">
          <div>
            <div class="w-12 h-12 rounded-xl bg-brand-navySec text-brand-blueHighlight flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2 group-hover:text-brand-blueHighlight transition-colors">
              Email Spam Checker
            </h3>
            <p class="text-sm text-brand-textDarkSec leading-relaxed">
              Analyze email copy, formatting, and DNS records to prevent messages from landing in the spam folder.
            </p>
          </div>
          <div class="mt-6 flex items-center gap-1.5 text-xs font-semibold text-brand-blueHighlight group-hover:translate-x-1 transition-transform">
            <span class="">Explore tool</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </div>
        </a>

        <!-- Tool 2: AI Email Writer -->
        <a href="#hero-section" class="other-tool-card glass-navy rounded-[22px] p-6 hover:-translate-y-2 hover:border-brand-blueHighlight transition-all duration-300 flex flex-col justify-between group border border-white/10">
          <div>
            <div class="w-12 h-12 rounded-xl bg-brand-navySec text-brand-blueHighlight flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2 group-hover:text-brand-blueHighlight transition-colors">
              AI Email Writer
            </h3>
            <p class="text-sm text-brand-textDarkSec leading-relaxed">
              Generate personalized, high-converting cold outreach and follow-up emails in seconds using trained AI models.
            </p>
          </div>
          <div class="mt-6 flex items-center gap-1.5 text-xs font-semibold text-brand-blueHighlight group-hover:translate-x-1 transition-transform">
            <span class="">Explore tool</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </div>
        </a>

        <!-- Tool 3: Gmail Generator -->
        <a href="#hero-section" class="other-tool-card glass-navy rounded-[22px] p-6 hover:-translate-y-2 hover:border-brand-blueHighlight transition-all duration-300 flex flex-col justify-between group border border-white/10">
          <div>
            <div class="w-12 h-12 rounded-xl bg-brand-navySec text-brand-blueHighlight flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2 group-hover:text-brand-blueHighlight transition-colors">
              Gmail Generator
            </h3>
            <p class="text-sm text-brand-textDarkSec leading-relaxed">
              Create and manage organized Gmail alias patterns and test variations for seamless inbox triage and account testing.
            </p>
          </div>
          <div class="mt-6 flex items-center gap-1.5 text-xs font-semibold text-brand-blueHighlight group-hover:translate-x-1 transition-transform">
            <span class="">Explore tool</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </div>
        </a>

        <!-- Tool 4: Email Checker -->
        <a href="#hero-section" class="other-tool-card glass-navy rounded-[22px] p-6 hover:-translate-y-2 hover:border-brand-blueHighlight transition-all duration-300 flex flex-col justify-between group border border-white/10">
          <div>
            <div class="w-12 h-12 rounded-xl bg-brand-navySec text-brand-blueHighlight flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2 group-hover:text-brand-blueHighlight transition-colors">
              Email Checker
            </h3>
            <p class="text-sm text-brand-textDarkSec leading-relaxed">
              Verify mailbox existence, SMTP response ping, MX records, and disposable email status before initiating delivery.
            </p>
          </div>
          <div class="mt-6 flex items-center gap-1.5 text-xs font-semibold text-brand-blueHighlight group-hover:translate-x-1 transition-transform">
            <span class="">Explore tool</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </div>
        </a>

      </div>

    </div>
  </section>

    <!-- =========================================================================
       SECTION 8: LIGHT FAQ (Exact Image 1 Design with Accordion Answers)
       Theme: Light Icy Blue (#F6FAFD)
       ========================================================================= -->
  <section id="faq" class="py-24 md:py-32 bg-brand-icy text-brand-textLightMain relative overflow-hidden">
    <div class="max-w-[880px] mx-auto px-5 sm:px-6">
      
      <!-- Section Header -->
      <div class="section-header text-center mb-16 space-y-4">
        <span class="inline-block text-xs font-bold tracking-widest text-brand-blue uppercase bg-brand-pale px-3 py-1 rounded-full border border-brand-borderLight">
          Common Inquiries
        </span>
        <h2 class="text-3xl sm:text-4xl md:text-[42px] font-bold text-brand-navy tracking-tight">
          Frequently Asked Questions
        </h2>
        <p class="text-base text-brand-textLightSec">
          Essential answers and policy guidelines for ExtractMails Reverse Email Lookup Tool.
        </p>
      </div>

      <!-- 8 Collapsed Question Rows with Real Answers & Accordion Toggle -->
      <div class="space-y-3.5">
        
        <!-- Q1 -->
        <div class="faq-accordion-item glass-white rounded-2xl p-5 sm:p-6 border border-brand-borderLight shadow-sm hover:border-brand-blue/30 transition-all cursor-pointer">
          <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-brand-navy pr-4">
              What is a Reverse Email Lookup tool?
            </h3>
            <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-blue flex items-center justify-center shrink-0">
              <svg class="faq-svg w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </div>
          </div>
          <div class="faq-answer-panel max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <p class="pt-3.5 text-sm sm:text-base leading-relaxed text-brand-textLightSec">
              The Reverse Email Lookup tool allows users to find information about a person or business by entering an email address.
            </p>
          </div>
        </div>

        <!-- Q2 -->
        <div class="faq-accordion-item glass-white rounded-2xl p-5 sm:p-6 border border-brand-borderLight shadow-sm hover:border-brand-blue/30 transition-all cursor-pointer">
          <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-brand-navy pr-4">
              How does Reverse Email Lookup work?
            </h3>
            <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-blue flex items-center justify-center shrink-0">
              <svg class="faq-svg w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </div>
          </div>
          <div class="faq-answer-panel max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <p class="pt-3.5 text-sm sm:text-base leading-relaxed text-brand-textLightSec">
              The Reverse Email Lookup tool scans public and verified data sources to match an email with available records, then compiles a detailed profile using smart matching logic.
            </p>
          </div>
        </div>

        <!-- Q3 -->
        <div class="faq-accordion-item glass-white rounded-2xl p-5 sm:p-6 border border-brand-borderLight shadow-sm hover:border-brand-blue/30 transition-all cursor-pointer">
          <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-brand-navy pr-4">
              Is Reverse Email Lookup legal to use?
            </h3>
            <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-blue flex items-center justify-center shrink-0">
              <svg class="faq-svg w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </div>
          </div>
          <div class="faq-answer-panel max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <p class="pt-3.5 text-sm sm:text-base leading-relaxed text-brand-textLightSec">
              Yes, when used for legitimate purposes such as verifying contacts, researching leads, or protecting yourself from scams. Always use it responsibly and ethically.
            </p>
          </div>
        </div>

        <!-- Q4 -->
        <div class="faq-accordion-item glass-white rounded-2xl p-5 sm:p-6 border border-brand-borderLight shadow-sm hover:border-brand-blue/30 transition-all cursor-pointer">
          <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-brand-navy pr-4">
              Are the results always accurate?
            </h3>
            <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-blue flex items-center justify-center shrink-0">
              <svg class="faq-svg w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </div>
          </div>
          <div class="faq-answer-panel max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <p class="pt-3.5 text-sm sm:text-base leading-relaxed text-brand-textLightSec">
              Results can be highly useful, but accuracy depends on how much reliable public and commercial data is available for the specific email address.
            </p>
          </div>
        </div>

        <!-- Q5 -->
        <div class="faq-accordion-item glass-white rounded-2xl p-5 sm:p-6 border border-brand-borderLight shadow-sm hover:border-brand-blue/30 transition-all cursor-pointer">
          <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-brand-navy pr-4">
              Can I look up any email address?
            </h3>
            <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-blue flex items-center justify-center shrink-0">
              <svg class="faq-svg w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </div>
          </div>
          <div class="faq-answer-panel max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <p class="pt-3.5 text-sm sm:text-base leading-relaxed text-brand-textLightSec">
              You can look up many business and publicly available email addresses, although results may vary when little public information exists.
            </p>
          </div>
        </div>

        <!-- Q6 -->
        <div class="faq-accordion-item glass-white rounded-2xl p-5 sm:p-6 border border-brand-borderLight shadow-sm hover:border-brand-blue/30 transition-all cursor-pointer">
          <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-brand-navy pr-4">
              Is my search data stored or shared?
            </h3>
            <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-blue flex items-center justify-center shrink-0">
              <svg class="faq-svg w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </div>
          </div>
          <div class="faq-answer-panel max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <p class="pt-3.5 text-sm sm:text-base leading-relaxed text-brand-textLightSec">
              Searches are processed server-side for the lookup request. Permanent storage and retention policies depend on your broader site implementation and analytics setup.
            </p>
          </div>
        </div>

        <!-- Q7 -->
        <div class="faq-accordion-item glass-white rounded-2xl p-5 sm:p-6 border border-brand-borderLight shadow-sm hover:border-brand-blue/30 transition-all cursor-pointer">
          <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-brand-navy pr-4">
              Is there a free version available?
            </h3>
            <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-blue flex items-center justify-center shrink-0">
              <svg class="faq-svg w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </div>
          </div>
          <div class="faq-answer-panel max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <p class="pt-3.5 text-sm sm:text-base leading-relaxed text-brand-textLightSec">
              Basic lookups may be offered for free, while premium plans can include advanced searches, bulk lookups, or deeper enrichment details.
            </p>
          </div>
        </div>

        <!-- Q8 -->
        <div class="faq-accordion-item glass-white rounded-2xl p-5 sm:p-6 border border-brand-borderLight shadow-sm hover:border-brand-blue/30 transition-all cursor-pointer">
          <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-brand-navy pr-4">
              Do I need an account to use the Reverse Email Lookup tool?
            </h3>
            <div class="w-8 h-8 rounded-full bg-brand-pale text-brand-blue flex items-center justify-center shrink-0">
              <svg class="faq-svg w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </div>
          </div>
          <div class="faq-answer-panel max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <p class="pt-3.5 text-sm sm:text-base leading-relaxed text-brand-textLightSec">
              You may be able to start with basic lookups without an account, but advanced reports, lookup history, and bulk features often require sign-in.
            </p>
          </div>
        </div>

      </div>

    </div>
  </section>


  <!-- =========================================================================
       SECTION 9: DARK FOOTER
       Theme: Dark Navy (#071C50)
       ========================================================================= -->

</main>

  <script>
    // Smooth Focus Trigger from "Try Now" CTA
    document.getElementById('tryNowBtn').addEventListener('click', function() {
      const input = document.getElementById('emailInput');
      input.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(() => {
        input.focus();
        input.classList.add('ring-4', 'ring-brand-blue');
        setTimeout(() => input.classList.remove('ring-4', 'ring-brand-blue'), 1200);
      }, 350);
    });

    // Lookup Demo Data Dictionary
    const demoProfiles = {
      'sarah.jenkins@acmecorp.io': {
        name: 'Sarah Jenkins',
        initials: 'SJ',
        role: 'VP of Strategic Partnerships',
        company: 'Acme Global Technologies • San Francisco, CA',
        confidence: 'Verified 99.8%',
        domain: 'acmecorp.io'
      },
      'david.choi@finscale.ai': {
        name: 'David Choi',
        initials: 'DC',
        role: 'Head of Engineering & AI Architecture',
        company: 'FinScale Intelligence • New York, NY',
        confidence: 'Verified 98.4%',
        domain: 'finscale.ai'
      }
    };

    function setDemoQuery(email, type) {
      const input = document.getElementById('emailInput');
      input.value = email;
      input.focus();
      handleLookupSubmit();
    }

    function handleLookupSubmit() {
      const input = document.getElementById('emailInput');
      const val = input.value.trim().toLowerCase();
      const btnText = document.getElementById('btnText');
      const btnSpinner = document.getElementById('btnSpinner');
      const msgBox = document.getElementById('lookupMessage');
      const resultBox = document.getElementById('demoResultBox');

      // Clear previous messages
      msgBox.className = "mt-2 text-xs flex items-center gap-1.5";
      msgBox.classList.add('hidden');

      // 1. Validation Check: Invalid format
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(val)) {
        msgBox.innerHTML = `
          <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span class="text-red-500 font-medium">Invalid email format. Please enter a valid corporate email.</span>
        `;
        msgBox.classList.remove('hidden');
        input.classList.add('border-red-400');
        return;
      }

      input.classList.remove('border-red-400');

      // 2. Loading State
      btnText.textContent = "Querying...";
      btnSpinner.classList.remove('hidden');

      setTimeout(() => {
        btnText.textContent = "Search";
        btnSpinner.classList.add('hidden');

        // Check for No Match
        if (val === 'unregistered@testmail.xyz' || val.includes('unregistered') || val.includes('unknown')) {
          msgBox.innerHTML = `
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-amber-600 font-medium">No match found. No verified public profile exists for this address.</span>
          `;
          msgBox.classList.remove('hidden');
          resultBox.innerHTML = `
            <div class="p-3 text-center text-xs text-brand-textLightSec">
              <span class="font-bold text-brand-navy">Lookup Completed:</span> Zero public registry matches found for <span class="font-mono text-brand-navy">${val}</span>.
            </div>
          `;
          return;
        }

        // 3. Render Sample Result (Defaulting or mapped)
        const profile = demoProfiles[val] || {
          name: val.split('@')[0].replace('.', ' ').replace(/\b\w/g, l => l.toUpperCase()),
          initials: val.slice(0, 2).toUpperCase(),
          role: 'Corporate Officer / Domain Administrator',
          company: val.split('@')[1] + ' • Enterprise Registry',
          confidence: 'Verified 95.2%',
          domain: val.split('@')[1]
        };

        resultBox.innerHTML = `
          <div class="flex items-center justify-between pb-2 mb-3 border-b border-brand-borderLight/80">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-brand-blue uppercase bg-white px-2.5 py-0.5 rounded border border-brand-borderLight">
              <span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span> Demo result
            </span>
            <span class="text-[11px] font-mono text-brand-textLightSec">Query: ${val}</span>
          </div>
          <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-full bg-brand-navy flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-inner">
              ${profile.initials}
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2">
                <h4 class="text-sm font-bold text-brand-navy truncate">${profile.name}</h4>
                <span class="text-[11px] bg-emerald-50 text-emerald-700 font-semibold px-2 py-0.5 rounded-full border border-emerald-200 shrink-0">${profile.confidence}</span>
              </div>
              <p class="text-xs font-medium text-brand-blue truncate">${profile.role}</p>
              <p class="text-xs text-brand-textLightSec truncate">${profile.company}</p>
              
              <div class="mt-2.5 pt-2 border-t border-slate-200/60 flex items-center gap-3 text-[11px] text-brand-textLightSec">
                <span class="flex items-center gap-1 text-slate-700 font-medium">
                  <svg class="w-3.5 h-3.5 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                  LinkedIn Public Profile
                </span>
                <span>•</span>
                <span class="text-slate-600 font-mono text-[10px]">Domain: ${profile.domain}</span>
              </div>
            </div>
          </div>
        `;

        msgBox.innerHTML = `
          <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <span class="text-emerald-600 font-medium">Record successfully resolved from live Apollo-indexed cache.</span>
        `;
        msgBox.classList.remove('hidden');

      }, 600);
    }
  
    

  
    // FAQ Accordion Toggle (Image 1 UI Interaction)
    document.querySelectorAll('.faq-accordion-item').forEach(function(item) {
      item.addEventListener('click', function() {
        const panel = item.querySelector('.faq-answer-panel');
        const svg = item.querySelector('.faq-svg');
        const isOpen = panel.style.maxHeight && panel.style.maxHeight !== '0px';

        // Close other items
        document.querySelectorAll('.faq-accordion-item').forEach(function(other) {
          const otherPanel = other.querySelector('.faq-answer-panel');
          const otherSvg = other.querySelector('.faq-svg');
          if (otherPanel) otherPanel.style.maxHeight = null;
          if (otherSvg) otherSvg.style.transform = 'rotate(0deg)';
        });

        // Toggle current item
        if (!isOpen) {
          panel.style.maxHeight = panel.scrollHeight + 'px';
          if (svg) svg.style.transform = 'rotate(45deg)';
        } else {
          panel.style.maxHeight = null;
          if (svg) svg.style.transform = 'rotate(0deg)';
        }
      });
    });

  
    // === MULTI-DIRECTIONAL CINEMATIC SCROLL REVEAL & CURSOR TRACKER ===
    (()=>{
      'use strict';
      const motion = matchMedia('(prefers-reduced-motion: reduce)');
      const pointer = matchMedia('(hover: hover) and (pointer: fine)');

      // 1. Dynamic Cursor Spotlight Tracking
      document.addEventListener('pointermove', event => {
        if (motion.matches || !pointer.matches) return;
        const card = event.target.closest('.glass-white, .glass-navy, #lookup-card, .faq-accordion-item, .step-pipeline-item');
        if (!card) return;
        const rect = card.getBoundingClientRect();
        const spotX = (event.clientX - rect.left).toFixed(1) + 'px';
        const spotY = (event.clientY - rect.top).toFixed(1) + 'px';
        card.style.setProperty('--spot-x', spotX);
        card.style.setProperty('--spot-y', spotY);
      }, { passive: true });

      if (motion.matches) return;
      if (!('IntersectionObserver' in window)) return;

      // 2. Selectors Configuration
      const upSelectors = [
        '#section-intro .section-header',
        '#section-definition .section-header',
        '#features .section-header',
        '#use-cases .section-header',
        '#how-it-works .section-header',
        '#other-tools .section-header',
        '#faq .section-header',
        '#how-it-works .step-active-card',
        '#how-it-works .step-pipeline-item'
      ];

      const leftSelectors = [
        '#section-intro .intro-content',
        '#section-definition .definition-visual'
      ];

      const rightSelectors = [
        '#section-intro .intro-visual',
        '#section-definition .definition-content'
      ];

      const scaleSelectors = [
        '#features .feature-card',
        '#use-cases .use-case-card',
        '#other-tools .other-tool-card',
        '.faq-accordion-item'
      ];

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      }, {
        rootMargin: '0px 0px -45px 0px',
        threshold: 0.08
      });

      function register(selectors, dirClass) {
        const els = document.querySelectorAll(selectors.join(', '));
        els.forEach(el => {
          el.classList.add('reveal-ready', dirClass);
          observer.observe(el);
        });
      }

      register(upSelectors, 'reveal-up');
      register(leftSelectors, 'reveal-left');
      register(rightSelectors, 'reveal-right');
      register(scaleSelectors, 'reveal-scale');

      // Stagger delays for grids
      const gridContainers = document.querySelectorAll(
        '#features .grid, #use-cases .grid, #other-tools .grid, #how-it-works .space-y-4, #faq .space-y-3\\.5'
      );
      gridContainers.forEach(container => {
        Array.from(container.children).forEach((child, idx) => {
          child.style.transitionDelay = ((idx % 6) * 0.09).toFixed(2) + 's';
        });
      });

      // Safety Viewport Check for elements in initial view
      requestAnimationFrame(() => {
        const vh = window.innerHeight;
        document.querySelectorAll('.reveal-ready:not(.is-visible)').forEach(el => {
          const rect = el.getBoundingClientRect();
          if (rect.top < vh - 40 && rect.bottom > 0) {
            el.classList.add('is-visible');
          }
        });
      });
    })();

  </script>



<?php
get_footer();
