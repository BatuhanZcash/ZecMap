# ZecMap

**The map of where you can actually spend Zcash, and the tools to start accepting it.**

Live site: **[zecmap.com](https://zecmap.com)**

ZecMap is a real, running directory of businesses that accept Zcash, with a world map, per country community pages, and a small payments toolkit that makes it easy both to pay with ZEC and to start accepting it. It is a community project, built with a plain, boring, dependable stack and no third party payment processor. Payments always go straight to the business wallet. ZecMap never touches the funds.

## Why

Zcash gives people private, low fee money. The missing piece has always been the everyday question: where can I use it, and how does a shop start taking it? ZecMap answers both.

## What it does

- **World map of Zcash communities.** Every business is placed on the map by its real location. Tap a country and you get a page listing the local businesses that accept ZEC. Countries are derived from each business's coordinates with an offline point in polygon lookup against Natural Earth boundaries, so there is no external geocoding service.
- **Business pages with real payments.** Each business page builds a [ZIP-321](https://zips.z.cash/zip-0321) payment URI and QR from the business's own shielded or unified address. A live price tool converts any amount in USD, EUR or TRY into ZEC and writes it straight into the QR and the wallet link.
- **Merchant toolkit.** A one click printable poster with the business's own QR and the Zcash logo, plus a step by step guide for owners that walks from getting a wallet to taking the first payment, using real screenshots.
- **Wallet guide.** A "from zero to your first shielded transaction" guide across four popular wallets, screen by screen, so newcomers understand shielded payments before they send anything.
- **Community owned listings.** Logged in users can suggest updates to any local listing (photo, links, hours, description). Changes go through admin review. Payment addresses are never changed from a public form, only by the verified owner reaching out, so a listing's funds destination cannot be hijacked.
- **Admin tools.** A country assignment tool, an update request queue, and the usual moderation screens.
- **Built for discovery.** A dynamic `sitemap.xml` exposes every country and business page to search engines.

## How Zcash is used

- Payment requests follow **ZIP-321** (`zcash:<address>?amount=...&memo=...`).
- Memos are only attached to addresses that can carry them (shielded `zs…` and unified `u1…`).
- The project treats shielded and unified addresses as the default, and is honest that a shielded payment cannot be confirmed from the outside, by design. The merchant confirms it in their own wallet.

## Tech

- Laravel 12, PHP 8.4
- MariaDB / MySQL
- Blade templates, vanilla JavaScript (no SPA framework)
- D3 and TopoJSON for the world map, rendered from bundled Natural Earth data
- `qrcode` for client side QR generation
- No AI services, no external payment processor, no tracking beyond basic self hosted analytics

## Run it locally

```bash
git clone https://github.com/BatuhanZcash/zecmap.git
cd zecmap
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
# set your DB in .env, then:
php artisan migrate
php artisan serve
```

Open http://127.0.0.1:8000.

## License

MIT. See [LICENSE](LICENSE).

---

Built by the community, for the Zcash community.
