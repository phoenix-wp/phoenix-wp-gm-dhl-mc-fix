<?php
/**
 * Bidirectional street / house number parsing (P1 stub).
 *
 * @package PhoenixWP\GmDhlMcFix
 */

declare(strict_types=1);

namespace PhoenixWP\GmDhlMcFix\Address;

defined( 'ABSPATH' ) || exit;

/**
 * Parses address line 1 into street and house number.
 */
final class Street_Parser {

	public const FORMAT_STREET_FIRST = 'street_first';
	public const FORMAT_NUMBER_FIRST = 'number_first';

	/**
	 * Parses a single address line.
	 *
	 * @param string $line Raw address line 1.
	 * @return array{street: string, house_no: string, format: string}|null
	 */
	public static function parse( string $line ): ?array {
		$line = trim( $line );
		if ( '' === $line ) {
			return null;
		}

		$street_first = self::match_street_first( $line );
		if ( null !== $street_first ) {
			return $street_first;
		}

		return self::match_number_first( $line );
	}

	/**
	 * German Market default pattern: "Musterstraße 12".
	 *
	 * @param string $line Address line.
	 * @return array{street: string, house_no: string, format: string}|null
	 */
	private static function match_street_first( string $line ): ?array {
		if ( ! preg_match( '/^([^\d]*[^\d\s])\s*(\d[\d\-\/\s]*[a-zA-Z]?)$/u', $line, $matches ) ) {
			return null;
		}

		return array(
			'street'   => trim( $matches[1] ),
			'house_no' => str_replace( ' ', '', trim( $matches[2] ) ),
			'format'   => self::FORMAT_STREET_FIRST,
		);
	}

	/**
	 * French / UK style: "12 Rue de la République".
	 *
	 * @param string $line Address line.
	 * @return array{street: string, house_no: string, format: string}|null
	 */
	private static function match_number_first( string $line ): ?array {
		if ( ! preg_match( '/^(\d[\d\-\/\s]*[a-zA-Z]?)\s+(.+)$/u', $line, $matches ) ) {
			return null;
		}

		return array(
			'street'   => trim( $matches[2] ),
			'house_no' => str_replace( ' ', '', trim( $matches[1] ) ),
			'format'   => self::FORMAT_NUMBER_FIRST,
		);
	}

	/**
	 * Normalizes to "Street Number" for German Market validators.
	 *
	 * @param string $line Raw address line.
	 */
	public static function normalize_for_german_market( string $line ): string {
		return self::to_street_with_number( $line );
	}

	/**
	 * Builds "Street Number" from a raw address line.
	 *
	 * @param string $line Raw address line 1.
	 */
	public static function to_street_with_number( string $line ): string {
		$line   = trim( $line );
		$parsed = self::parse( $line );

		if ( null === $parsed ) {
			return $line;
		}

		return trim( $parsed['street'] . ' ' . $parsed['house_no'] );
	}
}
