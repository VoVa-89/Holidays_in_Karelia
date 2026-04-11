<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Серверная математическая капча в сессии (без внешних API).
 */
final class MathCaptcha
{
    private const TTL_MINUTES = 15;

    /**
     * @return array{id: string, label: string}
     */
    public function issue(string $namespace): array
    {
        $id = Str::random(48);
        if (random_int(0, 1) === 1) {
            $a = random_int(1, 14);
            $b = random_int(1, 14);
            $answer = $a + $b;
            $label = "{$a} + {$b}";
        } else {
            $a = random_int(5, 20);
            $b = random_int(1, $a - 1);
            $answer = $a - $b;
            $label = "{$a} − {$b}";
        }

        $hash = hash_hmac('sha256', (string) $answer, (string) config('app.key'));
        $key = $this->sessionKey($namespace, $id);
        session([
            $key => [
                'hash' => $hash,
                'expires_at' => now()->addMinutes(self::TTL_MINUTES)->timestamp,
            ],
        ]);

        return ['id' => $id, 'label' => $label];
    }

    public function verify(string $namespace, string $id, mixed $answerInput): bool
    {
        $key = $this->sessionKey($namespace, $id);
        $data = session()->pull($key);
        if (! is_array($data) || ! isset($data['hash'], $data['expires_at'])) {
            return false;
        }

        if (now()->timestamp > (int) $data['expires_at']) {
            return false;
        }

        $answer = (int) preg_replace('/[^\-\d]/', '', (string) $answerInput);

        $expectedHash = hash_hmac('sha256', (string) $answer, (string) config('app.key'));

        return hash_equals((string) $data['hash'], $expectedHash);
    }

    private function sessionKey(string $namespace, string $id): string
    {
        return 'math_captcha.' . $namespace . '.' . $id;
    }
}
