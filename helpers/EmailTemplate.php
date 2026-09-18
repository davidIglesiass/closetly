<?php

/** Wraps an email's inner HTML in Closetly's branded shell - inline styles only, since external CSS isn't reliable in email clients. */
class EmailTemplate
{
    public static function wrap(string $bodyHtml): string
    {
        return '<!DOCTYPE html><html><body style="margin:0; background:#f5f2ea;">'
            . '<div style="background:#f5f2ea; padding:32px 16px; font-family:Arial,Helvetica,sans-serif; color:#17130f;">'
            . '<div style="max-width:480px; margin:0 auto; background:#ffffff; border:2px solid #17130f; border-radius:6px; overflow:hidden;">'
            . '<div style="background:#17130f; padding:20px 24px; text-align:center;">'
            . '<span style="font-family:Impact,Arial,sans-serif; font-size:26px; letter-spacing:0.04em; color:#f5f2ea;">CLOSETLY<span style="color:#ff4517;">.</span></span>'
            . '</div>'
            . '<div style="padding:28px 24px;">' . $bodyHtml . '</div>'
            . '<div style="background:#f5f2ea; padding:16px 24px; text-align:center; border-top:1px solid rgba(23,19,15,0.15);">'
            . '<p style="margin:0; font-size:11px; color:rgba(23,19,15,0.5); letter-spacing:0.03em;">CLOSETLY.COM &middot; Streetwear &amp; Graphic Tees</p>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</body></html>';
    }

    public static function button(string $url, string $label): string
    {
        return '<a href="' . htmlspecialchars($url) . '" style="display:inline-block; background:#ff4517; color:#f5f2ea; text-decoration:none; padding:12px 22px; border-radius:4px; font-weight:bold; font-size:14px;">'
            . htmlspecialchars($label) . '</a>';
    }

    public static function productLink(string $url, string $name): string
    {
        return '<a href="' . htmlspecialchars($url) . '" style="color:#ff4517; text-decoration:none; font-weight:bold;">'
            . htmlspecialchars($name) . '</a>';
    }
}
