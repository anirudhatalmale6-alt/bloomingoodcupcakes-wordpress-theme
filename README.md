# Bloomin' Good Cupcakes — WordPress theme

Bespoke one-page theme built to the approved redesign for Bloomin' Good
Cupcakes, Hethersett, Norwich. No page builder, no addon plugins, no licence
key: one stylesheet, one small script, and the photographs.

## Why it is built this way

The audit behind the redesign measured the site it replaces at **10.2 seconds**
to paint its largest photograph on a phone, against Google's 2.5 second
threshold, with 2,545 KiB of image savings available. A builder and its addons
would put most of that back before a single cupcake was on screen.

The five redesign fixes are in the markup rather than bolted on:

1. **One route to the order form from every scroll depth.** The old site's
   navigation read Home / Prices / Showcase on all six pages and the order form
   was not one of them. The header here is sticky and carries *Start your order*
   on phones as well as desktop.
2. **One price list.** Five products were published at two different prices
   depending which page you read. Every figure now comes from one Customizer
   field, so the page cannot disagree with itself.
3. **The food hygiene rating, cited and checkable** — authority, date, and a
   link to the public register.
4. **The bouquets at a size the piping reads at**, and every photograph is the
   client's own. The share card is a real bouquet, not the stock macarons the
   old site declared.
5. **The buying questions answered beside the products** rather than on the
   terms page: allergies, payment, keeping them, colour variation.

## The form

Store-before-send. The enquiry is written to the database *before* any attempt
to email it, because `wp_mail()` returning true only means the message was
handed to the mailer. Enquiries live under **Enquiries** in wp-admin, and the
list shows whether each one was emailed.

Two spam traps, a honeypot and a time trap. Both **flag** an enquiry rather than
discarding it — testing on the build site showed the discard version telling me
"thank you, that has come through" while storing nothing, and for a business
whose enquiries are its orders, losing one is far worse than filing one that
turns out to be junk.

Three required fields: name, email, message. The old form asked for more than
thirty, four of them required, including a phone number and a date — even from
someone who had just said they only had a question.

## Editing

Appearance → Customize → Bloomin' Good Cupcakes:

- **Phone, email and address** — the number becomes a tap-to-call link everywhere
- **Notice bar** — one line above the header; empty means the bar does not appear
- **Food hygiene rating** — rating, authority, date, register link
- **Prices** — one line per item, `Name | £Price`
- **Where enquiries go** — blank uses the site admin address

Every field ships with the approved copy as its default, so an empty box means
*unchanged*, never *missing*.

## Installing

The theme folder goes in `wp-content/themes/`. The two files in `_mu-plugins/`
go in `wp-content/mu-plugins/` — see the README in that folder for why they are
not part of the theme. Exclude `_mu-plugins` and `.git` when building the
deployment zip.

## Google reviews

`inc/reviews.php` reads the listing through the **Places API** and caches it for
twelve hours. That is a different thing from the Business Profile API, which is
what replies to reviews and is gated behind an approval the owner has to request
— displaying reviews needs only a Cloud key with Places enabled.

Places returns at most five reviews and Google picks which. The section only
switches from the written quotes to the live feed once at least `reviews_min`
(three by default) are available, because the listing carried **one** review at
the time of the audit and a feed showing one review advertises the weakness
rather than hiding it. Configure it now; it turns itself on when the reviews
arrive.

## Requirements

WordPress 6.0+, PHP 7.4+.
