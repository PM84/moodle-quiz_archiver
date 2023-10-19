<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file defines the TimeStampProtocolClient class.
 *
 * @package   quiz_archiver
 * @copyright 2023 Niels Gandraß <niels@gandrass.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quiz_archiver;

defined('MOODLE_INTERNAL') || die();

/**
 * A client implementation for the Time-Stamp Protocol (TSP) as defined by RFC 3161.
 *
 * @see https://www.ietf.org/rfc/rfc3161.txt For more information about TSP
 */
class TimeStampProtocolClient {

    /** @var string Content-Type header for TimeStampQuery */
    const CONTENT_TYPE_TIMESTAMP_QUERY = 'application/timestamp-query';

    /** @var string Content-Type header for TimeStampReply */
    const CONTENT_TYPE_TIMESTAMP_REPLY = 'application/timestamp-reply';

    /**
     * Creates a new TimeStampProtocolClient instance.
     *
     * @param string $server_url URL of the TSP server
     */
    public function __construct(string $server_url) {
        $this->server_url = $server_url;
    }

    /**
     * @return string URL of the TSP server
     */
    public function get_server_url() {
        return $this->server_url;
    }

    /**
     * Signs the given hexadecimal SHA256 hash using the Time-Stamp Protocol
     *
     * @param string $sha256hash Hexadecimal SHA256 hash of the data to be signed
     * @return array Associative array containing the binary ASN.1/DER encoded
     *               TimeStampRequest and the TimeStampReply
     * @throws \Exception If an error occurs while sending the request or
     *                    invalid data was received
     */
    public function sign(string $sha256hash): array {
        // Prepare TimeStampRequest
        $nonce = self::generateNonce();
        $tsreq = self::createTimeStampReq($sha256hash, $nonce);

        // Send TimeStampRequest to TSP server
        $ch = curl_init();
        $curl_error = null;
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->server_url,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $tsreq,
            CURLOPT_HTTPHEADER => [
                'Content-Type: ' . self::CONTENT_TYPE_TIMESTAMP_QUERY,
                'Content-Length: ' . strlen($tsreq),
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
        ]);
        $tsresp = curl_exec($ch);

        if (curl_error($ch)) {
            $curl_error = curl_error($ch);
        } else {
            $curl_info = curl_getinfo($ch);
        }

        curl_close($ch);

        // Error handling
        if ($curl_error !== null) {
            // TODO: Localize
            throw new \Exception('Error while sending request to TSP server: ' . $curl_error);
        }

        if ($curl_info['http_code'] !== 200) {
            // TODO: Localize
            throw new \Exception('TSP server returned HTTP status code ' . $curl_info['http_code']);
        }

        if ($curl_info['content_type'] !== self::CONTENT_TYPE_TIMESTAMP_REPLY) {
            // TODO: Localize
            throw new \Exception('TSP server returned unexpected content type ' . $curl_info['content_type']);
        }

        return [
            'query' => $tsreq,
            'reply' => $tsresp
        ];
    }

    /**
     * Generates a 128-bit nonce.
     *
     * @return string 128-bit nonce
     * @throws \Exception If an appropriate source of randomness cannot be found.
     */
    public static function generateNonce(): string {
        return random_bytes(16);
    }

    /**
     * Creates a TimeStampReq message for the given SHA256 hash.
     *
     * @see https://github.com/edoceo/radix-rfc3161 This code is largely based
     * on the implementation of Edoceo, Inc. (Licensed under the MIT License)
     *
     * @param string $sha256hash Hexadecimal SHA256 hash of the data to be signed
     * @param string $nonce 128-bit nonce to be used in the TimeStampReq
     * @param bool $requestTSAPublicKey Whether to request the TSA's public key
     * @return string ASN.1 encoded TimeStampReq
     * @throws \ValueError If the SHA256 hash or nonce are invalid
     */
    protected static function createTimeStampReq(string $sha256hash, string $nonce, bool $requestTSAPublicKey = false): string {
        // Validate input
        if (strlen($sha256hash) !== 64) {
            throw new \ValueError('Invalid hexadecimal SHA256 hash');
        }
        if (strlen($nonce) !== 16) {
            throw new \ValueError('Invalid nonce');
        }

        // Generate ASN.1 encoded TimeStampReq
        $asn1 = [];
        // -> Root DER SEQUENCE
        $asn1[0] = chr(0x00) . chr(0x00); // SEQUENCE OF + Length (TBD)
        // -> TimeStampRequest Version (INTEGER v1)
        $asn1[1] = chr(0x02) . chr(0x01) . chr(0x01); // INTEGER + Length + Value
        // -> MessageImprint
        $asn1[2] = chr(0x00) . chr(0x00); // SEQUENCE OF + Length (TBD)
        $asn1[3] = chr(0x30) . chr(0x0d); // SEQUENCE OF + Length (0x0d == 13)
        // -> MessageImprint / Object ID, Length 0x09
        $asn1[4] = chr(0x06) . chr(0x09) // OBJECT IDENTIFIER (length 9 bytes)
            . chr(0x60) // 2 . 16
            . chr(0x86) . chr(0x48) // 840
            . chr(0x01) . chr(0x65) // 1 . 101
            . chr(0x03) . chr(0x04) // 3 . 4
            . chr(0x02) . chr(0x01) // 2 . 1
            . chr(0x05) . chr(0x00); // OID Terminator == NULL + Length (0x00)

        // -> MessageImprint / Hash Value, Length 0x40
        $asn1[5] = chr(0x04) . chr(0x20) . hex2bin($sha256hash); // OCTET STRING 0x42 == 32 Bytes (SHA256) + Hash value

        // -> Nonce
        $asn1[] = chr(0x02) . chr(0x10) . $nonce; // INTEGER + Length (16 bytes) + nonce value

        // -> certReq
        if ($requestTSAPublicKey) {
            $asn1[] = chr(0x01) . chr(0x01) . chr(0xff); // BOOLEAN + Length + True
        }

        // Set correct message length metadata
        // -> MessageImprint
        $asn1[2] = chr(0x30) . chr(strlen($asn1[3] . $asn1[4] . $asn1[5]));

        // -> Root DER SEQUENCE
        $asn1[0] = chr(0x30) . chr(strlen(implode('', array_slice($asn1, 1))));

        // Build final ASN.1 encoded TimeStampReq
        return implode('', $asn1);
    }

}
