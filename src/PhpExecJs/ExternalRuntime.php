<?php

declare(strict_types=1);

namespace Koriym\Baracoa\PhpExecJs;

use Override;
use RuntimeException;
use Symfony\Component\Process\Process;

use function array_filter;
use function assert;
use function exec;
use function explode;
use function file_exists;
use function file_put_contents;
use function getenv;
use function is_dir;
use function is_executable;
use function is_string;
use function is_writable;
use function json_decode;
use function json_encode;
use function mkdir;
use function register_shutdown_function;
use function rtrim;
use function sprintf;
use function str_starts_with;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

use const DIRECTORY_SEPARATOR;
use const PATH_SEPARATOR;
use const PHP_OS;

/**
 * External JavaScript runtime using Node.js
 */
final class ExternalRuntime implements RuntimeInterface
{
    /** @var list<string> */
    private array $temporaryFiles = [];
    private string|null $context = null;
    private string|null $binaryPath;

    /**
     * @param list<string>         $binary Binary names to search for
     * @param array<string, mixed> $env    Environment variables
     */
    public function __construct(
        private readonly string $name = 'Node.js (V8)',
        private readonly array $binary = ['node', 'nodejs'],
        private readonly array|null $env = null,
        private int|false $timeout = false,
    ) {
        $this->binaryPath = $this->findBinaryPath();
        register_shutdown_function([$this, 'removeTemporaryFiles']);
    }

    public function __destruct()
    {
        $this->removeTemporaryFiles();
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function isAvailable(): bool
    {
        return $this->binaryPath !== null;
    }

    #[Override]
    public function evalJs(string $code): mixed
    {
        assert($this->binaryPath !== null, 'Runtime must be available');

        $encodedCode = json_encode($code);
        assert($encodedCode !== false);
        $code = 'return eval(' . $encodedCode . ');';
        if ($this->context !== null) {
            $code = $this->context . "\n" . $code;
        }

        $code = $this->embedInRuntime($code);
        $sourceFile = $this->createTemporaryFile($code, 'js');

        $command = $this->binaryPath;
        if (str_starts_with(PHP_OS, 'WIN')) {
            $command = '"' . $this->binaryPath . '"';
        }

        $command .= ' ' . $sourceFile;

        [$status, $stdout, $stderr] = $this->executeCommand($command);
        $this->checkProcessStatus($status, $stdout, $stderr, $command);

        /** @var array{0: string, 1?: mixed} $decoded */
        $decoded = json_decode($stdout, true);
        $this->checkEvalStatus($decoded[0], $decoded[1] ?? null);

        return $decoded[1] ?? null;
    }

    public function createContext(string $code): void
    {
        $this->context = $code;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function removeTemporaryFiles(): void
    {
        foreach ($this->temporaryFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $this->temporaryFiles = [];
    }

    private function embedInRuntime(string $code): string
    {
        return <<<JS
(function(program, execJS) { execJS(program) })(function(global, module, exports, require, console, setTimeout, setInterval, clearTimeout, clearInterval, setImmediate, clearImmediate) { $code;
}, function(program) {
  var output, print = function(string) {
    process.stdout.write('' + string);
  };
  try {
    result = program();
    if (typeof result == 'undefined' && result !== null) {
      print('["ok"]');
    } else {
      try {
        print(JSON.stringify(['ok', result]));
      } catch (err) {
        print(JSON.stringify(['err', '' + err, err.stack]));
      }
    }
  } catch (err) {
    print(JSON.stringify(['err', '' + err, err.stack]));
  }
});
JS;
    }

    private function createTemporaryFile(string $content, string $extension): string
    {
        $dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
        if (! is_dir($dir)) {
            if (@mkdir($dir, 0777, true) === false && ! is_dir($dir)) {
                throw new RuntimeException(sprintf("Unable to create directory: %s\n", $dir));
            }
        } elseif (! is_writable($dir)) {
            throw new RuntimeException(sprintf("Unable to write in directory: %s\n", $dir));
        }

        $filename = $dir . DIRECTORY_SEPARATOR . uniqid('koriym_baracoa_phpexecjs', true) . '.' . $extension;
        file_put_contents($filename, $content);
        $this->temporaryFiles[] = $filename;

        return $filename;
    }

    private function findBinaryPath(): string|null
    {
        $pathStr = getenv('PATH');
        if ($pathStr === false) {
            $pathStr = '';
        }

        $paths = array_filter(explode(PATH_SEPARATOR, $pathStr));
        foreach ($paths as $path) {
            foreach ($this->binary as $binary) {
                $binaryPath = $path . DIRECTORY_SEPARATOR . $binary;
                if (is_executable($binaryPath)) {
                    return $binaryPath;
                }

                if (str_starts_with(PHP_OS, 'WIN')) {
                    $binaryPath .= '.exe';
                    if (is_executable($binaryPath)) {
                        return $binaryPath;
                    }
                }
            }
        }

        foreach ($this->binary as $binary) {
            $whichBinaryPath = exec('which ' . $binary);
            if ($whichBinaryPath !== false && $whichBinaryPath !== '') {
                return $whichBinaryPath;
            }
        }

        return null;
    }

    private function checkProcessStatus(int $status, string $stdout, string $stderr, string $command): void
    {
        if ($status !== 0 && $stderr !== '') {
            throw new RuntimeException(sprintf(
                'The exit status code \'%s\' says something went wrong:' . "\n"
                . 'stderr: "%s"' . "\n"
                . 'stdout: "%s"' . "\n"
                . 'command: %s.',
                $status,
                $stderr,
                $stdout,
                $command,
            ));
        }
    }

    private function checkEvalStatus(string $statusEval, mixed $result): void
    {
        if ($statusEval === 'ok') {
            return;
        }

        $encoded = json_encode($result);
        $resultStr = $encoded !== false ? $encoded : 'unknown';

        throw new RuntimeException(sprintf(
            'Something went wrong evaluating JS code:' . "\n"
            . 'result: "%s"',
            $resultStr,
        ));
    }

    /** @return array{int, string, string} */
    private function executeCommand(string $command): array
    {
        $process = Process::fromShellCommandline($command, null, $this->env);

        if ($this->timeout !== false) {
            $process->setTimeout($this->timeout);
        }

        $process->run();

        return [
            $process->getExitCode() ?? 1,
            $process->getOutput(),
            $process->getErrorOutput(),
        ];
    }
}
