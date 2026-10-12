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

add_action(
    'wp_head',
    static function () {
        ?>
<style id="extractmail-homepage-hover-reset">
	.extractmail-page a:hover,
	.extractmail-page a:focus,
	.extractmail-page a:active {
		background-color: transparent !important;
	}

	.extractmail-page a:not([class*="text-white"]):hover,
	.extractmail-page a:not([class*="text-white"]):focus,
	.extractmail-page a:not([class*="text-white"]):active {
		color: inherit !important;
	}

	.extractmail-page a[class*="text-white"]:hover,
	.extractmail-page a[class*="text-white"]:focus,
	.extractmail-page a[class*="text-white"]:active {
		color: #ffffff !important;
	}

	.extractmail-page button.extractmail-step,
	.extractmail-page button.extractmail-step:hover,
	.extractmail-page button.extractmail-step:focus,
	.extractmail-page button.extractmail-step:active {
		background: transparent !important;
		border-color: transparent !important;
		box-shadow: none !important;
		color: inherit !important;
		outline: none;
	}

	.extractmail-page #extractmail-apollo-reverse-lookup-submit:hover,
	.extractmail-page #extractmail-apollo-reverse-lookup-submit:focus,
	.extractmail-page #extractmail-apollo-reverse-lookup-submit:active {
		background: linear-gradient(271deg, #383CFF 1.04%, #030458 65.23%) !important;
		border-color: transparent !important;
		color: #ffffff !important;
		outline: none;
	}

	.extractmail-page #extractmail-apollo-reverse-lookup-form-section {
		scroll-margin-top: 110px;
	}

	.extractmail-page .extractmail-try-now-link,
	.extractmail-page .extractmail-try-now-link:hover,
	.extractmail-page .extractmail-try-now-link:focus,
	.extractmail-page .extractmail-try-now-link:active {
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
    100
);

add_action(
    'wp_head',
    static function () {
        ?>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap');

	@keyframes extractmail-spin {
		to {
			transform: rotate(360deg);
		}
	}

	.extractmail-lookup-spinner {
		width: 22px;
		height: 22px;
		border: 3px solid rgba(37, 99, 235, 0.18);
		border-top-color: #2563eb;
		border-radius: 999px;
		animation: extractmail-spin 0.8s linear infinite;
	}

	#extractmail-apollo-reverse-lookup-form input[type="email"],
	#extractmail-apollo-reverse-lookup-submit {
		min-height: 42px;
	}

	#extractmail-apollo-reverse-lookup-submit {
		line-height: 1;
	}

	.extractmail-faq-content {
		transition: max-height 0.3s ease;
	}

	.extractmail-glass-card {
		backdrop-filter: blur(14px);
	}
</style>
<?php
    },
    1
);

get_header();
?>

<main class="extractmail-page min-h-screen bg-white font-body text-ink">
	<section class="py-12 md:py-10">
		<div class="mx-auto max-w-[1500px] px-4 md:px-6">
			<div class="grid items-center gap-8 md:grid-cols-2 md:gap-4">
				<div>
					<span
						class="mt-12 inline-block text-[14px] text-[#8eb7f3] md:mb-8 md:text-[18px] lg:mt-4 lg:text-[22px]">
						<a href="<?php echo esc_url(home_url('/')); ?>"
							class="hover:underline">Home</a> /
						<a href="<?php echo esc_url(home_url('/tools/')); ?>"
							class="hover:underline">Tools</a> /
						<a href="<?php echo esc_url(home_url('/tools/reverse-email-lookup/')); ?>"
							class="hover:underline">Reverse Email Lookup Tool</a>
					</span>

					<h1
						class="mb-4 bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[28px] font-semibold leading-snug text-transparent sm:text-[34px] md:mb-6 md:text-[42px] md:leading-tight lg:text-[49px]">
						Reverse Email Lookup Tool
					</h1>

					<p
						class="max-w-xl text-[14px] leading-relaxed text-gray-600 sm:text-[16px] md:mb-8 md:text-[18px] lg:text-[20px]">
						Convert any professional email into actionable insight with our Apollo-powered Reverse Email
						Lookup Tool. Discover names, companies, and public profile details in a clean, fast workflow.
					</p>

					<button type="button"
						class="extractmail-try-now-link mt-12 inline-flex rounded-full bg-gradient-to-r from-blue-700 to-green-400 px-6 py-1 text-[14px] text-white md:mb-8 md:px-12 md:text-[18px] lg:mt-4 lg:text-[22px]">
						Try Now
					</button>
				</div>

				<div class="mt-6 flex justify-center md:mt-6 md:justify-end">
					<img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-62-3.png"
						alt="Reverse email lookup illustration"
						class="w-full max-w-[300px] object-contain sm:max-w-[400px] md:max-w-[480px] lg:max-w-[520px]" />
				</div>
			</div>
		</div>
	</section>

	<section id="extractmail-apollo-reverse-lookup-form-section" class="px-4 py-6">
		<div class="mx-auto max-w-[1200px]">
			<div
				class="overflow-hidden rounded-[28px] border border-[#d9e3ff] bg-white shadow-[0_20px_60px_rgba(56,60,255,0.08)]">
				<div class="border-b border-[#e8eeff] bg-[linear-gradient(180deg,#f6f8ff_0%,#eef3ff_100%)] px-6 py-5">
					<span
						class="inline-flex rounded-full border border-[#97b6ff] bg-white/80 px-6 py-2 text-center text-[11px] font-semibold tracking-[0.26em] text-[#2f5cff] shadow-[0_8px_20px_rgba(56,60,255,0.08)] sm:px-10">
						EMAIL LOOKUP
					</span>
				</div>

				<div class="p-6 md:p-7">
					<form id="extractmail-apollo-reverse-lookup-form" method="post" class="space-y-4">
						<?php wp_nonce_field('extractmail_apollo_reverse_lookup', 'extractmail_apollo_reverse_lookup_nonce'); ?>
						<input type="hidden" name="extractmail_apollo_reverse_lookup_submit" value="1" />

						<label for="extractmail-apollo-lookup-email" class="block text-sm font-medium text-slate-600">
							Who&apos;s behind this email address?
						</label>

						<div
							class="flex flex-col gap-3 rounded-[22px] border border-[#dbe5ff] bg-[#f8faff] p-3 sm:flex-row sm:items-center">
							<input id="extractmail-apollo-lookup-email" name="lookup_email" type="email"
								value="<?php echo esc_attr($extractmail_apollo_reverse_lookup_email); ?>"
								placeholder="name@company.com"
								class="flex-1 rounded-[16px] border border-[#cad7ff] bg-white px-4 py-2.5 text-sm text-slate-700 shadow-[inset_0_1px_2px_rgba(15,23,42,0.03)] placeholder:text-slate-400 focus:border-[#4b68ff] focus:outline-none focus:ring-2 focus:ring-[#b6c7ff]"
								required />

							<button id="extractmail-apollo-reverse-lookup-submit" type="submit"
								class="rounded-[16px] bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] px-6 py-2.5 text-sm font-semibold text-white shadow-[0_12px_24px_rgba(56,60,255,0.22)] transition hover:opacity-95">
								Search
							</button>
						</div>

						<p class="mt-3 text-xs leading-relaxed text-slate-400">
							Best for professional email addresses.
						</p>
					</form>

					<div id="extractmail-apollo-reverse-lookup-message"
						class="mt-4 hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
					</div>

					<div id="extractmail-apollo-reverse-lookup-loading"
						class="mt-6 hidden rounded-[24px] border border-blue-100 bg-white px-6 py-5 shadow-sm">
						<div class="flex items-center gap-4">
							<div class="extractmail-lookup-spinner" aria-hidden="true"></div>
							<div>
								<p class="text-[18px] font-bold text-ink">Searching professional profile data...</p>
								<p class="mt-1 text-[15px] text-slate-500">Matching the submitted email against Apollo
									person and company records.</p>
							</div>
						</div>
					</div>

					<div id="extractmail-apollo-reverse-lookup-results">
						<?php echo extractmail_apollo_reverse_lookup_render_results($extractmail_apollo_reverse_lookup_result); ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="bg-[#f4f4f4] px-4 py-20">
		<div class="mx-auto max-w-[1200px] text-center">
			<h2 class="mb-6 text-[28px] font-medium leading-snug text-gray-900 md:text-[38px]">
				Find Names, Profiles &amp; Details from Any Email Address
			</h2>

			<p class="mb-14 text-[14px] leading-relaxed text-gray-600 md:text-[17px]">
				Turn any email address into meaningful and actionable insights with our Reverse Email Lookup tool that
				helps users uncover the identity behind an email instantly, accurately, and securely. Reverse Email
				Lookup gives you everything you need in seconds, whether verifying leads, avoiding scams, or enriching
				your contact data.
			</p>

			<h2 class="mb-5 text-[24px] font-medium text-gray-900 md:text-[37px]">
				What is Reverse Email Lookup Tool
			</h2>

			<p class="text-[14px] leading-relaxed text-gray-600 md:text-[17px]">
				Reverse Email Lookup is one of the most powerful tools in the email extraction section, allowing users
				to input an email address and retrieve information associated with it, such as the owner&apos;s name,
				company details, and social profiles. The tool removes guesswork and offers clearer, actionable data to
				help you make smarter decisions.
			</p>
		</div>
	</section>

	<section class="py-10">
		<div class="mx-auto max-w-[1400px] px-6 text-center">
			<h2
				class="mb-4 bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[36px] font-semibold leading-snug text-transparent">
				Key Features of Reverse Email <br /> Lookup Tool
			</h2>

			<p class="mb-10 text-[14px] leading-relaxed text-gray-600 md:text-[15px]">
				The Reverse Email Lookup tool from ExtractMails is built to deliver faster, more accurate, and
				actionable insights while removing unnecessary complexity. Every feature is designed to help you uncover
				the identity behind an email address and make smarter decisions with confidence.
			</p>

			<div class="grid gap-10 md:grid-cols-4 md:gap-0">
				<div class="relative px-6 text-center">
					<img src="https://www.extractmails.com/wp-content/uploads/2026/04/instant-messaging-1.png"
						alt="Instant email lookup" class="mx-auto mb-6 w-20" />
					<h3 class="mb-3 text-[18px] font-bold text-gray-900">
						Instant Email Lookup
					</h3>
					<p class="text-[15px] leading-relaxed text-gray-600">
						The Reverse Email Lookup tool offers results within seconds. Enter an email address and receive
						a detailed profile of the sender almost instantly.
					</p>
					<div class="absolute right-0 top-1/2 hidden h-[90%] w-px -translate-y-1/2 bg-gray-300 md:block">
					</div>
				</div>

				<div class="relative px-6 text-center">
					<img src="https://www.extractmails.com/wp-content/uploads/2026/04/id-card-1.png"
						alt="Identity discovery" class="mx-auto mb-6 w-20" />
					<h3 class="mb-3 text-[18px] font-bold text-gray-900">
						Identity Discovery
					</h3>
					<p class="text-[15px] leading-relaxed text-gray-600">
						The Reverse Email Lookup tool helps you go beyond the address itself and uncover the real
						identity behind the email.
					</p>
					<div class="absolute right-0 top-1/2 hidden h-[90%] w-px -translate-y-1/2 bg-gray-300 md:block">
					</div>
				</div>

				<div class="relative px-6 text-center">
					<img src="https://www.extractmails.com/wp-content/uploads/2026/04/dashboard-1.png"
						alt="Professional and company insights" class="mx-auto mb-6 w-20" />
					<h3 class="mb-3 text-[18px] font-bold text-gray-900">
						Professional &amp; Company Insights
					</h3>
					<p class="text-[15px] leading-relaxed text-gray-600">
						Our specialized tool helps you access valuable professional and company information associated
						with an email address.
					</p>
					<div class="absolute right-0 top-1/2 hidden h-[90%] w-px -translate-y-1/2 bg-gray-300 md:block">
					</div>
				</div>

				<div class="px-6 text-center">
					<img src="https://www.extractmails.com/wp-content/uploads/2026/04/bussiness-man-1.png"
						alt="Social and online profiles" class="mx-auto mb-6 w-20" />
					<h3 class="mb-3 text-[18px] font-bold text-gray-900">
						Social &amp; Online Profiles
					</h3>
					<p class="text-[15px] leading-relaxed text-gray-600">
						The Reverse Email Lookup tool also helps you discover the digital footprint linked to an email
						address.
					</p>
				</div>
			</div>
		</div>
	</section>

	<section class="relative mx-auto my-10 max-w-[1500px] overflow-hidden py-16">
		<div class="absolute left-[24px] top-[425px] h-[272px] w-[687px] bg-[#b1c3ff] blur-[147px]"></div>
		<div class="absolute left-[225px] top-[237px] h-[175px] w-[372px] bg-[#b1c3ff] blur-[147px]"></div>
		<div
			class="absolute left-[658.32px] top-[390.31px] h-[161.15px] w-[715.56px] origin-top-left rotate-[-18.579deg] bg-[#98b0ff] blur-[147px]">
		</div>
		<div
			class="absolute left-[969px] top-[-8.93px] h-[54.57px] w-[260.74px] origin-top-left rotate-[-18.579deg] bg-[#98b0ff]/40 blur-[47.7px]">
		</div>
		<div
			class="absolute left-[998.32px] top-[1190.31px] h-[161.15px] w-[315.56px] origin-top-left rotate-[-18.579deg] bg-[#98b0ff] blur-[97px]">
		</div>
		<div
			class="absolute left-0 top-[1108.93px] h-[184.57px] w-[560.74px] origin-top-left rotate-[-18.579deg] bg-[#98b0ff] blur-[87.7px]">
		</div>
		<div
			class="absolute left-[985.53px] top-[666.46px] h-[82.15px] w-[525.08px] origin-top-left rotate-[-9.961deg] bg-[#00c979] blur-[107px]">
		</div>
		<div
			class="absolute left-[-56px] top-[847.02px] h-[55.97px] w-[153.89px] origin-top-left rotate-[-9.961deg] bg-[#00c979] blur-[81px]">
		</div>
		<div
			class="absolute left-[-219.02px] top-[-118.97px] h-[94.17px] w-[519.35px] origin-top-left rotate-[48.581deg] bg-[#00c979] blur-[147px]">
		</div>
		<div
			class="absolute left-[985.53px] top-[1566.46px] h-[82.15px] w-[525.08px] origin-top-left rotate-[-9.961deg] bg-[#98b0ff] blur-[107px]">
		</div>
		<div
			class="absolute left-[96px] top-[1547.02px] h-[55.97px] w-[153.89px] origin-top-left rotate-[-9.961deg] bg-[#00c979] blur-[81px]">
		</div>

		<div class="relative mx-auto max-w-6xl px-6">
			<div class="mx-auto mt-0 max-w-3xl text-center">
				<h2 class="mb-8 text-[26px] font-semibold leading-tight text-gray-800 md:text-[30px]">
					Why Use Reverse Email <br /> Lookup Tool
				</h2>

				<p class="mb-8 text-[14px] leading-relaxed text-gray-600 md:text-[17px]">
					Emails are everywhere on the internet, but not all of them are trustworthy. The Reverse Email Lookup
					tool offers several advantages, including:
				</p>
			</div>

			<div class="mx-auto max-w-5xl space-y-10">
				<div
					class="extractmail-glass-card grid items-center gap-8 rounded-3xl bg-white p-8 md:grid-cols-[1.5fr_0.5fr] md:p-10">
					<div>
						<h3 class="mb-3 text-lg font-semibold text-indigo-700 md:text-xl">
							Identify Unknown Senders
						</h3>

						<p class="mb-4 text-sm leading-relaxed text-gray-600">
							Have you ever received an email from someone you don&apos;t recognize? The tool helps solve
							this instantly by uncovering the identity of the sender and reducing risk before you reply.
						</p>

						<p class="mb-2 text-sm text-gray-700">
							Reverse Email Lookup helps you with:
						</p>

						<ul class="list-disc space-y-1 pl-5 text-sm text-gray-600">
							<li>Uncovering the name of the person behind the email address.</li>
							<li>Discovering the online presence of the sender.</li>
							<li>Understanding the sender before responding.</li>
						</ul>
					</div>

					<div>
						<img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-16-2026-03_11_05-PM-1.png"
							alt="Identify unknown senders" class="mx-auto w-full max-w-sm rounded-2xl" />
					</div>
				</div>

				<div
					class="extractmail-glass-card grid items-center gap-8 rounded-3xl bg-white p-8 md:grid-cols-[0.5fr_1.5fr] md:p-10">
					<div class="flex-1">
						<img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-16-2026-03_12_47-PM-1.png"
							alt="Verify leads and prospects" class="mx-auto w-full max-w-sm rounded-2xl" />
					</div>

					<div class="flex-1">
						<h3 class="mb-3 text-lg font-semibold text-indigo-700 md:text-xl">
							Verify Leads &amp; Prospects
						</h3>

						<p class="mb-4 text-sm leading-relaxed text-gray-600">
							The Reverse Email Lookup tool is especially useful for sales professionals. Reaching the
							wrong contact wastes time and lowers conversion rates.
						</p>

						<p class="mb-2 text-sm text-gray-700">
							With Reverse Email Lookup, you can:
						</p>

						<ul class="list-disc space-y-1 pl-5 text-sm text-gray-600">
							<li>Confirm whether the email belongs to a real person or prospect.</li>
							<li>Identify the company and role behind the address.</li>
							<li>Prioritize high-quality prospects and increase conversion rates.</li>
						</ul>
					</div>
				</div>

				<div
					class="extractmail-glass-card grid items-center gap-8 rounded-3xl bg-white p-8 md:grid-cols-[1.5fr_0.5fr] md:p-10">
					<div>
						<h3 class="mb-3 text-lg font-semibold text-indigo-700 md:text-xl">
							Avoid Scams &amp; Fraud
						</h3>

						<p class="mb-4 text-sm leading-relaxed text-gray-600">
							A suspicious email can look legitimate at first glance. Reverse Email Lookup helps reduce
							cyber threats and phishing attempts.
						</p>

						<p class="mb-2 text-sm text-gray-700">
							The tool acts as your first line of defense by helping you:
						</p>

						<ul class="list-disc space-y-1 pl-5 text-sm text-gray-600">
							<li>Detect fake or disposable email addresses.</li>
							<li>Analyze domain credibility.</li>
							<li>Spot inconsistencies in the sender&apos;s identity.</li>
						</ul>
					</div>

					<div>
						<img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-16-2026-03_14_47-PM-1.png"
							alt="Avoid scams and fraud" class="mx-auto w-full max-w-sm rounded-2xl" />
					</div>
				</div>

				<div
					class="extractmail-glass-card grid items-center gap-8 rounded-3xl bg-white p-8 md:grid-cols-[0.5fr_1.5fr] md:p-10">
					<div class="flex-1">
						<img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-16-2026-03_16_37-PM-1.png"
							alt="Enrich CRM data" class="mx-auto w-full max-w-sm rounded-2xl" />
					</div>

					<div class="flex-1">
						<h3 class="mb-3 text-lg font-semibold text-indigo-700 md:text-xl">
							Enrich Your CRM Data
						</h3>

						<p class="mb-4 text-sm leading-relaxed text-gray-600">
							In sales and marketing, incomplete data is a common challenge. The tool helps transform a
							single data point into a richer profile.
						</p>

						<p class="mb-2 text-sm text-gray-700">
							The Reverse Email Lookup tool helps in extracting:
						</p>

						<ul class="list-disc space-y-1 pl-5 text-sm text-gray-600">
							<li>Full names from email addresses.</li>
							<li>Job title and company details.</li>
							<li>Social and professional profiles.</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="py-6">
		<div class="mx-auto max-w-[1500px] rounded-lg bg-white px-6 py-2 text-center">
			<h2
				class="mb-8 bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[32px] font-semibold text-transparent md:text-[44px]">
				How Reverse Email Lookup Tool <br /> Works?
			</h2>

			<p class="mx-auto max-w-3xl text-sm leading-relaxed text-gray-600 md:text-base">
				The Reverse Email Lookup tool from ExtractMails is designed to offer fast, intuitive, and powerful
				responses. Our tool features a simple interface backed by a sophisticated lookup system.
			</p>

			<p class="mt-4 text-sm text-gray-500">
				Here&apos;s a closer look at how the process works:
			</p>

			<div class="relative mt-12 hidden md:block">
				<div class="absolute left-0 top-1/2 w-full -translate-y-1/2 border-t-2 border-dashed border-gray-800">
				</div>

				<div class="relative flex items-center justify-evenly">
					<button type="button" class="extractmail-step mt-3 flex flex-col items-center" data-step="1"
						aria-pressed="true">
						<span
							class="z-10 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-b from-[#030458] to-[#383CFF] text-white">1</span>
						<img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt=""
							class="extractmail-step-arrow mt-1 h-4 w-4" />
					</button>

					<button type="button" class="extractmail-step flex flex-col items-center" data-step="2"
						aria-pressed="false">
						<span
							class="z-10 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-b from-[#030458] to-[#383CFF] text-white">2</span>
						<img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt=""
							class="extractmail-step-arrow mt-1 hidden h-4 w-4" />
					</button>

					<button type="button" class="extractmail-step flex flex-col items-center" data-step="3"
						aria-pressed="false">
						<span
							class="z-10 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-b from-[#030458] to-[#383CFF] text-white">3</span>
						<img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt=""
							class="extractmail-step-arrow mt-1 hidden h-4 w-4" />
					</button>

					<button type="button" class="extractmail-step flex flex-col items-center" data-step="4"
						aria-pressed="false">
						<span
							class="z-10 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-b from-[#030458] to-[#383CFF] text-white">4</span>
						<img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt=""
							class="extractmail-step-arrow mt-1 hidden h-4 w-4" />
					</button>

					<button type="button" class="extractmail-step flex flex-col items-center" data-step="5"
						aria-pressed="false">
						<span
							class="z-10 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-b from-[#030458] to-[#383CFF] text-white">5</span>
						<img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt=""
							class="extractmail-step-arrow mt-1 hidden h-4 w-4" />
					</button>

					<button type="button" class="extractmail-step flex flex-col items-center" data-step="6"
						aria-pressed="false">
						<span
							class="z-10 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-b from-[#030458] to-[#383CFF] text-white">6</span>
						<img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" alt=""
							class="extractmail-step-arrow mt-1 hidden h-4 w-4" />
					</button>
				</div>
			</div>
		</div>

		<div class="mx-auto mt-10 max-w-[1300px] px-6">
			<div class="extractmail-step-content" data-content="1">
				<div class="relative rounded-2xl bg-[#daf8ec] p-8 pt-10">
					<div class="absolute left-0 top-3 h-2 w-full rounded-t-2xl bg-white"></div>
					<h3 class="mb-4 px-4 text-[24px] font-semibold text-[#1f2a7a] md:text-[29px]">
						Enter the Email Address
					</h3>
					<p class="px-4">
						The process starts by entering the email address you want to investigate into the search bar.
					</p>
					<div class="absolute bottom-3 left-0 h-2 w-full rounded-t-2xl bg-white"></div>
				</div>
			</div>

			<div class="extractmail-step-content hidden" data-content="2">
				<div class="relative rounded-2xl bg-[#daf8ec] p-8 pt-10">
					<div class="absolute left-0 top-3 h-2 w-full rounded-t-2xl bg-white"></div>
					<h3 class="mb-4 px-4 text-[24px] font-semibold text-[#1f2a7a] md:text-[29px]">
						Initiate the Lookup
					</h3>
					<p class="px-4">
						Once you click the Search button, the tool begins processing your request in real time. At this
						stage, our system:
					</p>
					<ul class="list-disc pl-10 pt-4 text-gray-700">
						<li>Validates the email structure and domain.</li>
						<li>Checks whether the address looks active and deliverable.</li>
						<li>Identifies provider and hosting signals where available.</li>
					</ul>
					<div class="absolute bottom-3 left-0 h-2 w-full rounded-t-2xl bg-white"></div>
				</div>
			</div>

			<div class="extractmail-step-content hidden" data-content="3">
				<div class="relative rounded-2xl bg-[#daf8ec] p-8 pt-10">
					<div class="absolute left-0 top-3 h-2 w-full rounded-t-2xl bg-white"></div>
					<h3 class="mb-4 px-4 text-[24px] font-semibold text-[#1f2a7a] md:text-[29px]">
						Scan Multiple Data Sources
					</h3>
					<p class="px-4">
						These sources may include:
					</p>
					<ul class="list-disc pl-10 pt-4 text-gray-700">
						<li>Publicly available web data.</li>
						<li>Professional and social profiles.</li>
						<li>Business directories and company records.</li>
					</ul>
					<div class="absolute bottom-3 left-0 h-2 w-full rounded-t-2xl bg-white"></div>
				</div>
			</div>

			<div class="extractmail-step-content hidden" data-content="4">
				<div class="relative rounded-2xl bg-[#daf8ec] p-8 pt-10">
					<div class="absolute left-0 top-3 h-2 w-full rounded-t-2xl bg-white"></div>
					<h3 class="mb-4 px-4 text-[24px] font-semibold text-[#1f2a7a] md:text-[29px]">
						Apply Smart Matching Algorithms
					</h3>
					<p class="px-4">
						Our Reverse Email Lookup tool uses advanced matching techniques to connect the dots accurately
						and helps to:
					</p>
					<ul class="list-disc pl-10 pt-4 text-gray-700">
						<li>Link email addresses to real identities.</li>
						<li>Filter out irrelevant or outdated information.</li>
						<li>Score matches based on confidence.</li>
						<li>Eliminate duplicate or inconsistent results.</li>
					</ul>
					<div class="absolute bottom-3 left-0 h-2 w-full rounded-t-2xl bg-white"></div>
				</div>
			</div>

			<div class="extractmail-step-content hidden" data-content="5">
				<div class="relative rounded-2xl bg-[#daf8ec] p-8 pt-10">
					<div class="absolute left-0 top-3 h-2 w-full rounded-t-2xl bg-white"></div>
					<h3 class="mb-4 px-4 text-[24px] font-semibold text-[#1f2a7a] md:text-[29px]">
						Generate a Detailed Profile
					</h3>
					<p class="px-4">
						In just a few seconds, the lookup tool compiles verified information into a clean, easy-to-read
						report. Depending on availability, results may include:
					</p>
					<ul class="list-disc pl-10 pt-4 text-gray-700">
						<li>Full name and profile image.</li>
						<li>Company and job role.</li>
						<li>Location details.</li>
						<li>Social media and professional links.</li>
						<li>Website or portfolio.</li>
						<li>Email validity and risk indicators.</li>
					</ul>
					<div class="absolute bottom-3 left-0 h-2 w-full rounded-t-2xl bg-white"></div>
				</div>
			</div>

			<div class="extractmail-step-content hidden" data-content="6">
				<div class="relative rounded-2xl bg-[#daf8ec] p-8 pt-10">
					<div class="absolute left-0 top-3 h-2 w-full rounded-t-2xl bg-white"></div>
					<h3 class="mb-4 px-4 text-[24px] font-semibold text-[#1f2a7a] md:text-[29px]">
						Review Insights Instantly
					</h3>
					<p class="px-4">
						The Reverse Email Lookup tool from ExtractMails is designed to offer fast, intuitive, and
						powerful responses. With our tool, you can immediately:
					</p>
					<ul class="list-disc pl-10 pt-4 text-gray-700">
						<li>Identify the person or business behind the email.</li>
						<li>Assess credibility and authenticity.</li>
					</ul>
					<div class="absolute bottom-3 left-0 h-2 w-full rounded-t-2xl bg-white"></div>
				</div>
			</div>
		</div>
	</section>

	<section class="py-10">
		<div class="mx-auto max-w-[1300px] px-4">
			<h2
				class="mb-12 text-center text-[32px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent md:text-[40px] lg:text-[45px]">
				Other Tools
			</h2>

			<div class="rounded-xl border border-gray-300 bg-white p-8">
				<div class="relative grid gap-8 md:grid-cols-2">
					<div class="absolute left-1/2 top-0 hidden h-full w-px bg-gray-300 md:block"></div>

					<div class="space-y-8 pr-4 md:pr-8">
						<div>
							<h3 class="mb-2 font-semibold text-gray-800">
								Email Spam Checker
							</h3>
							<p class="text-sm leading-relaxed text-gray-600">
								Analyze emails for spam triggers and improve deliverability instantly with
								<a href="https://www.extractmails.com/tools/email-spam-checker/"
									class="text-indigo-600 hover:underline">
									Email Spam Checker &rsaquo;
								</a>
							</p>
						</div>

						<div>
							<h3 class="mb-2 font-semibold text-gray-800">
								AI Email Writer
							</h3>
							<p class="text-sm leading-relaxed text-gray-600">
								Generate personalized and professional emails instantly with the
								<a href="<?php echo esc_url(home_url('/tools/ai-email-writer/')); ?>"
									class="text-indigo-600 hover:underline">
									AI Email Writer &rsaquo;
								</a>
							</p>
						</div>
					</div>

					<div class="space-y-8 pl-4 md:pl-8">
						<div>
							<h3 class="mb-2 font-semibold text-gray-800">
								Gmail Generator
							</h3>
							<p class="text-sm leading-relaxed text-gray-600">
								Create custom or temporary Gmail addresses for testing and quick usage with the
								<a href="https://www.extractmails.com/tools/gmail-generator/"
									class="text-indigo-600 hover:underline">
									Gmail Generator tool &rsaquo;
								</a>
							</p>
						</div>

						<div>
							<h3 class="mb-2 font-semibold text-gray-800">
								Email Checker
							</h3>
							<p class="text-sm leading-relaxed text-gray-600">
								Verify the validity of an email, along with its format and activity, with
								<a href="<?php echo esc_url(home_url('/tools/email-checker/')); ?>"
									class="text-indigo-600 hover:underline">
									Email Checker &rsaquo;
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-[1400px] px-6 py-0">
		<h2
			class="mb-12 text-center text-[32px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent md:text-[40px] lg:text-[45px]">
			Frequently Asked Questions
		</h2>

		<div class="space-y-6">
			<div class="extractmail-faq-item cursor-pointer">
				<div class="rounded-[20px] bg-gradient-to-r from-black to-blue-600 p-[2px]">
					<div class="rounded-[18px] bg-white px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">
						<div class="flex items-center justify-between gap-4">
							<h3
								class="bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[16px] font-semibold text-transparent md:text-[22px]">
								What is a Reverse Email Lookup tool?
							</h3>
							<span
								class="extractmail-faq-icon text-[26px] font-light transition-transform duration-300">+</span>
						</div>
						<div class="extractmail-faq-content max-h-0 overflow-hidden">
							<p class="mt-4 text-[14px] text-gray-500">
								The Reverse Email Lookup tool allows users to find information about a person or
								business by entering an email address.
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="extractmail-faq-item cursor-pointer">
				<div class="rounded-[20px] bg-gradient-to-r from-black to-blue-600 p-[2px]">
					<div class="rounded-[18px] bg-white px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">
						<div class="flex items-center justify-between gap-4">
							<h3
								class="bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[16px] font-semibold text-transparent md:text-[22px]">
								How does Reverse Email Lookup work?
							</h3>
							<span
								class="extractmail-faq-icon text-[26px] font-light transition-transform duration-300">+</span>
						</div>
						<div class="extractmail-faq-content max-h-0 overflow-hidden">
							<p class="mt-4 text-[14px] text-gray-500">
								The Reverse Email Lookup tool scans public and verified data sources to match an email
								with available records, then compiles a detailed profile using smart matching logic.
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="extractmail-faq-item cursor-pointer">
				<div class="rounded-[20px] bg-gradient-to-r from-black to-blue-600 p-[2px]">
					<div class="rounded-[18px] bg-white px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">
						<div class="flex items-center justify-between gap-4">
							<h3
								class="bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[16px] font-semibold text-transparent md:text-[22px]">
								Is Reverse Email Lookup legal to use?
							</h3>
							<span
								class="extractmail-faq-icon text-[26px] font-light transition-transform duration-300">+</span>
						</div>
						<div class="extractmail-faq-content max-h-0 overflow-hidden">
							<p class="mt-4 text-[14px] text-gray-500">
								Yes, when used for legitimate purposes such as verifying contacts, researching leads, or
								protecting yourself from scams. Always use it responsibly and ethically.
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="extractmail-faq-item cursor-pointer">
				<div class="rounded-[20px] bg-gradient-to-r from-black to-blue-600 p-[2px]">
					<div class="rounded-[18px] bg-white px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">
						<div class="flex items-center justify-between gap-4">
							<h3
								class="bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[16px] font-semibold text-transparent md:text-[22px]">
								Are the results always accurate?
							</h3>
							<span
								class="extractmail-faq-icon text-[26px] font-light transition-transform duration-300">+</span>
						</div>
						<div class="extractmail-faq-content max-h-0 overflow-hidden">
							<p class="mt-4 text-[14px] text-gray-500">
								Results can be highly useful, but accuracy depends on how much reliable public and
								commercial data is available for the specific email address.
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="extractmail-faq-item cursor-pointer">
				<div class="rounded-[20px] bg-gradient-to-r from-black to-blue-600 p-[2px]">
					<div class="rounded-[18px] bg-white px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">
						<div class="flex items-center justify-between gap-4">
							<h3
								class="bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[16px] font-semibold text-transparent md:text-[22px]">
								Can I look up any email address?
							</h3>
							<span
								class="extractmail-faq-icon text-[26px] font-light transition-transform duration-300">+</span>
						</div>
						<div class="extractmail-faq-content max-h-0 overflow-hidden">
							<p class="mt-4 text-[14px] text-gray-500">
								You can look up many business and publicly available email addresses, although results
								may vary when little public information exists.
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="extractmail-faq-item cursor-pointer">
				<div class="rounded-[20px] bg-gradient-to-r from-black to-blue-600 p-[2px]">
					<div class="rounded-[18px] bg-white px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">
						<div class="flex items-center justify-between gap-4">
							<h3
								class="bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[16px] font-semibold text-transparent md:text-[22px]">
								Is my search data stored or shared?
							</h3>
							<span
								class="extractmail-faq-icon text-[26px] font-light transition-transform duration-300">+</span>
						</div>
						<div class="extractmail-faq-content max-h-0 overflow-hidden">
							<p class="mt-4 text-[14px] text-gray-500">
								Searches are processed server-side for the lookup request. Permanent storage and
								retention policies depend on your broader site implementation and analytics setup.
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="extractmail-faq-item cursor-pointer">
				<div class="rounded-[20px] bg-gradient-to-r from-black to-blue-600 p-[2px]">
					<div class="rounded-[18px] bg-white px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">
						<div class="flex items-center justify-between gap-4">
							<h3
								class="bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[16px] font-semibold text-transparent md:text-[22px]">
								Is there a free version available?
							</h3>
							<span
								class="extractmail-faq-icon text-[26px] font-light transition-transform duration-300">+</span>
						</div>
						<div class="extractmail-faq-content max-h-0 overflow-hidden">
							<p class="mt-4 text-[14px] text-gray-500">
								Basic lookups may be offered for free, while premium plans can include advanced
								searches, bulk lookups, or deeper enrichment details.
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="extractmail-faq-item cursor-pointer">
				<div class="rounded-[20px] bg-gradient-to-r from-black to-blue-600 p-[2px]">
					<div class="rounded-[18px] bg-white px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">
						<div class="flex items-center justify-between gap-4">
							<h3
								class="bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-[16px] font-semibold text-transparent md:text-[22px]">
								Do I need an account to use the Reverse Email Lookup tool?
							</h3>
							<span
								class="extractmail-faq-icon text-[26px] font-light transition-transform duration-300">+</span>
						</div>
						<div class="extractmail-faq-content max-h-0 overflow-hidden">
							<p class="mt-4 text-[14px] text-gray-500">
								You may be able to start with basic lookups without an account, but advanced reports,
								lookup history, and bulk features often require sign-in.
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const form = document.getElementById('extractmail-apollo-reverse-lookup-form');
		const submitButton = document.getElementById('extractmail-apollo-reverse-lookup-submit');
		const results = document.getElementById('extractmail-apollo-reverse-lookup-results');
		const messageBox = document.getElementById('extractmail-apollo-reverse-lookup-message');
		const loadingBox = document.getElementById('extractmail-apollo-reverse-lookup-loading');
		const tryNowLink = document.querySelector('.extractmail-page .extractmail-try-now-link');
		const lookupSection = document.getElementById('extractmail-apollo-reverse-lookup-form-section');

		if (tryNowLink && lookupSection) {
			tryNowLink.addEventListener('click', function(event) {
				const header = document.querySelector('.site-header, #masthead, header');
				const headerHeight = header ? header.getBoundingClientRect().height : 0;
				const scrollGap = Math.max(headerHeight + 28, 110);
				const targetTop = lookupSection.getBoundingClientRect().top + window.pageYOffset -
					scrollGap;

				event.preventDefault();
				window.scrollTo({
					top: Math.max(targetTop, 0),
					behavior: 'smooth'
				});

			});
		}

		if (!form || !submitButton || !results || !messageBox || !loadingBox) {
			return;
		}

		const defaultButtonText = submitButton.textContent;

		function showMessage(message) {
			messageBox.textContent = message;
			messageBox.classList.remove('hidden');
		}

		function hideMessage() {
			messageBox.textContent = '';
			messageBox.classList.add('hidden');
		}

		function showLoading() {
			loadingBox.classList.remove('hidden');
			results.style.display = 'none';
		}

		function hideLoading() {
			loadingBox.classList.add('hidden');
			results.style.display = '';
		}

		function setActiveStep(id) {
			const steps = document.querySelectorAll('.extractmail-step');
			const contents = document.querySelectorAll('.extractmail-step-content');

			steps.forEach(function(step) {
				const isActive = step.dataset.step === id;
				step.classList.toggle('mt-3', isActive);
				step.setAttribute('aria-pressed', isActive ? 'true' : 'false');

				const arrow = step.querySelector('.extractmail-step-arrow');
				if (arrow) {
					arrow.classList.toggle('hidden', !isActive);
				}
			});

			contents.forEach(function(content) {
				content.classList.toggle('hidden', content.dataset.content !== id);
			});
		}

		form.addEventListener('submit', function(event) {
			event.preventDefault();
			hideMessage();
			showLoading();

			submitButton.disabled = true;
			submitButton.textContent = 'Searching';
			submitButton.style.opacity = '0.85';
			submitButton.style.cursor = 'wait';

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
				.then(function(response) {
					if (!response.ok) {
						throw new Error('Request failed.');
					}

					return response.json();
				})
				.then(function(payload) {
					if (!payload || !payload.success || !payload.data || typeof payload.data.html !==
						'string') {
						throw new Error('Unexpected response.');
					}

					results.innerHTML = payload.data.html;
				})
				.catch(function() {
					showMessage(
					'The reverse lookup request could not be completed. Please try again.');
				})
				.finally(function() {
					hideLoading();
					submitButton.disabled = false;
					submitButton.textContent = defaultButtonText;
					submitButton.style.opacity = '1';
					submitButton.style.cursor = 'pointer';
				});
		});

		document.querySelectorAll('.extractmail-step').forEach(function(step) {
			step.addEventListener('click', function() {
				setActiveStep(step.dataset.step);
			});
		});

		document.querySelectorAll('.extractmail-faq-item').forEach(function(item) {
			item.addEventListener('click', function() {
				const content = item.querySelector('.extractmail-faq-content');
				const icon = item.querySelector('.extractmail-faq-icon');

				document.querySelectorAll('.extractmail-faq-item').forEach(function(otherItem) {
					if (otherItem === item) {
						return;
					}

					const otherContent = otherItem.querySelector(
						'.extractmail-faq-content');
					const otherIcon = otherItem.querySelector('.extractmail-faq-icon');

					if (otherContent) {
						otherContent.style.maxHeight = null;
					}

					if (otherIcon) {
						otherIcon.textContent = '+';
					}
				});

				if (!content || !icon) {
					return;
				}

				if (content.style.maxHeight) {
					content.style.maxHeight = null;
					icon.textContent = '+';
				} else {
					content.style.maxHeight = content.scrollHeight + 'px';
					icon.textContent = '-';
				}
			});
		});

		setActiveStep('1');
	});
</script>

<?php
get_footer();
?>
