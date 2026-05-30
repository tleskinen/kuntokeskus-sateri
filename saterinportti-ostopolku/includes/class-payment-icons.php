<?php
/**
 * Maksutapojen SVG-ikonit.
 *
 * HUOM: Nämä ovat prototyyppivaiheen *approksimaatioita* — yksinkertaisia
 * SVG-renderöintejä jotka tekevät logoista tunnistettavia mutta eivät ole
 * brändien virallisia tiedostoja. Tuotantoon nämä korvataan Visma Pay
 * -kumppaniportaalin virallisilla SVG-paketeilla, ja liikuntaetujen
 * (Smartum/Epassi/Edenred) viralliset partner-assetit.
 *
 * Yhtenäinen viewBox 0 0 100 30 — kuvasuhde 100:30 toimii sekä leveissä
 * tekstilogoissa että ympyrätyyppisissä merkeissä.
 *
 * @package Saterinportti\Ostopolku
 */

namespace Saterinportti\Ostopolku;

defined( 'ABSPATH' ) || exit;

final class Payment_Icons {

	/**
	 * Palauttaa maksutavan ikonin inline-SVG:nä. Jos avain on tuntematon,
	 * palauttaa tyhjän stringin (template skippaa renderöinnin).
	 */
	public static function svg( string $key ): string {
		switch ( $key ) {
			case 'nordea':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Nordea"><text x="50" y="22" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="800" font-size="18" fill="#0000a0">Nordea</text></svg>';

			case 'op':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="OP"><circle cx="50" cy="15" r="13" fill="#fc8c00"/><circle cx="50" cy="15" r="7" fill="#fff"/><circle cx="50" cy="15" r="3" fill="#fc8c00"/></svg>';

			case 'danske':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Danske Bank"><rect x="10" y="6" width="80" height="18" fill="#003755" rx="1"/><text x="50" y="18.5" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="700" font-size="9" fill="#fff" letter-spacing=".5">DANSKE BANK</text></svg>';

			case 'pop':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="POP Pankki"><text x="50" y="22" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="800" font-size="14" fill="#3aa835">POP Pankki</text><path d="M85 10 Q88 7 91 11 Q88 15 85 12 Z" fill="#3aa835"/></svg>';

			case 'saastopankki':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Säästöpankki"><g transform="translate(18,7) scale(.45)"><path d="M16 0 C18 8 24 8 24 16 C24 22 20 24 16 24 C12 24 8 22 8 16 C8 8 14 8 16 0 Z" fill="#7cc7d1"/></g><text x="56" y="20" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="700" font-size="11" fill="#005d8a">Säästöpankki</text></svg>';

			case 'omasp':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Oma Säästöpankki"><text x="50" y="22" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="800" font-size="18" fill="#2bb24c" font-style="italic">omaSP</text></svg>';

			case 'aktia':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Aktia"><text x="50" y="23" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="900" font-size="20" fill="#000">Aktia</text></svg>';

			case 'alandsbanken':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ålandsbanken"><text x="50" y="25" text-anchor="middle" font-family="serif" font-weight="900" font-size="26" fill="#006e51">Å</text></svg>';

			case 'spankki':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="S-Pankki"><text x="22" y="24" text-anchor="middle" font-family="cursive,Georgia,serif" font-weight="700" font-style="italic" font-size="22" fill="#3aa835">S</text><text x="65" y="21" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="700" font-size="14" fill="#3aa835">PANKKI</text></svg>';

			case 'mastercard':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Mastercard"><circle cx="42" cy="15" r="11" fill="#eb001b"/><circle cx="58" cy="15" r="11" fill="#f79e1b" opacity=".88"/></svg>';

			case 'visa':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Visa"><text x="50" y="23" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="900" font-style="italic" font-size="22" fill="#1a1f71" letter-spacing="1">VISA</text></svg>';

			case 'mobilepay':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="MobilePay"><rect x="10" y="6" width="14" height="18" rx="2.5" fill="#5a78ff"/><rect x="13" y="9" width="8" height="9" fill="#fff"/><text x="62" y="21" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="700" font-size="12" fill="#5a78ff">MobilePay</text></svg>';

			case 'siirto':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Siirto"><rect x="20" y="4" width="60" height="22" fill="#f0a020" rx="2"/><text x="50" y="20" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="800" font-size="13" fill="#fff">Siirto.</text></svg>';

			case 'epassi':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="ePassi"><text x="50" y="22" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="800" font-size="18" fill="#e6007e">epassi</text></svg>';

			case 'smartum':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Smartum"><text x="50" y="22" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="700" font-size="17" fill="#0a0a0a">smartum</text></svg>';

			case 'edenred':
				return '<svg viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Edenred"><circle cx="50" cy="15" r="13" fill="#ed1c24"/><text x="50" y="18.5" text-anchor="middle" font-family="-apple-system,system-ui,Arial,sans-serif" font-weight="700" font-style="italic" font-size="8" fill="#fff">Edenred</text></svg>';
		}

		return '';
	}
}
