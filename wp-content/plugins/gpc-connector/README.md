# GPC Connector

One WordPress plugin that replaces the seven separate GuestPostCRM plugins. It writes to the CRMs through `index.php?entryPoint=smart_gateway` instead of `get_post_all`, and it no longer needs `/web/html/global_utilsfinal.php`.

| Feature             | Replaces                    | What it does                                                                      |
| ------------------- | --------------------------- | --------------------------------------------------------------------------------- |
| Backlink sync       | Rightee Backlink Tracker    | On post save, upserts newly added links into `outr_seo_backlinksmain`             |
| Published blog sync | Published Blog Notification | On publish (and updates while published), upserts the post into `outr_published_blogs` |
| Post API            | My Post API                 | `my-api/v1/create-post`, `post-status`, `link-status`, `anchor-occurrences`       |
| Backlink remover    | Rightee Backlink Remover    | `rightee/v1/remove-backlink`                                                      |
| Backlink checker    | Backlink Checker API        | `backlink-checker/v1/check`                                                       |
| Blog link extractor | Blog Link Extractor API     | `blog-link-extractor/v2/links`                                                    |
| Backlink scanner    | Backlink Scanner            | Tools → Backlink Scanner and the `/backlinks/` report                            |

You can switch each feature on or off under **Settings → GPC Connector**.

## Installing on a site

1. Upload the `gpc-connector` folder to `wp-content/plugins/` and activate it.
2. Go to **Settings → GPC Connector** and enter the **signing secret**. This is the same value as `VITE_SMARTGATEWAY_SECRET_KEY` in the CRM front end. You can put it in `wp-config.php` instead:
   ```php
   define('GPC_CONNECTOR_GATEWAY_SECRET', '...');
   ```
3. Click **Run test**. Every enabled CRM and module should show ✓.
4. Deactivate the old plugins. The plugin warns you if any of them are still active, because running both syncs each post twice.

## CRM connection

Every call is a JSON `POST` to the CRM's `smart_gateway` URL. Each call carries an `X-Api-Token` header built the same way as `generateToken()` in `guestpostcrm/src/services/api.js`:

```
base64( '{"ts":<unix time>,"source":"claude-mcp"}' + '||' + hex HMAC-SHA256(json, secret) )
```

Each sync does a `fetch` filtered on the match field, then an `update` (if a record was found) or a `create`. The match fields are the same ones the old plugins used, so existing CRM records are updated, not duplicated:

- Backlinks match on `name` = `<site host>_<post id>_<md5(link url)>`.
- Published blogs match on `blog_url`.

The default routing matches the old `global_utilsfinal.php`:

| CRM      | Enabled | Backlinks | Published blogs |
| -------- | ------- | --------- | --------------- |
| outright | yes     | yes       | yes             |
| sales    | yes     | yes       |                 |
| kartikey | no      | yes       |                 |
| testcrm  | no      | yes       |                 |

You can add CRMs, remove them or change their routing on the settings page.

The sync no longer sends `date_entered`, `date_modified` or `deleted`. The CRM sets these itself, so an update no longer overwrites the date the record was created. Every other field is the same as before.

## Inbound endpoints

The routes, headers, request bodies and responses are unchanged. The API keys default to the keys the old plugins used, and you can change them on the settings page.

| Endpoint                                            | Header                                   |
| --------------------------------------------------- | ---------------------------------------- |
| `POST /wp-json/my-api/v1/create-post` (+ 3 GET routes) | `X-API-Key` (plus `X-GPC-User-Email`) |
| `POST /wp-json/rightee/v1/remove-backlink`          | `X-API-Key`                              |
| `POST /wp-json/backlink-checker/v1/check`           | `X-API-Key`                              |
| `GET /wp-json/blog-link-extractor/v2/links?url=`    | `X-BLE-API-Key` or `?api_key=`           |

CORS (`Access-Control-Allow-Origin: *`) is now added only to these four namespaces. The old plugins added it to every REST route on the site.

## Other changes from the old plugins

- The backlink sync sends only links that are new. The old tracker re-sent every link in the post on every save. Now:
  - **Editing a published post:** only links whose URL wasn't in the published version are sent.
  - **Draft or scheduled post:** every link counts as new, because nothing is live yet.
  - **First publish:** every link is re-sent, so its CRM record gets the public URL instead of `?p=123`.

  Because unchanged links are no longer re-sent, saving a post no longer resets their `status_c` to `Added` or moves their `expiry_date_c` forward.
- The `/backlinks/` report requires a logged-in user who can edit posts. Before, anyone could open it.
- The scanner's admin buttons use nonces. Removing a link checks that the user can edit that particular post.
- If a CRM times out during a backlink sync, it is skipped for the rest of that save, so the editor doesn't hang once per link.
- The two popups after saving and publishing are now a single popup. It reports sync failures with the CRM's error message.
