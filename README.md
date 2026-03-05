<p align="center">
    <img src="logo.png" height="300" alt="Terminal Detector">
</p>

# Terminal Detector

Detects which terminal emulator is running the current PHP process.

> **Requires [PHP 8.2+](https://php.net/releases/)**

## Installation

```bash
composer require shipfastlabs/terminal-detector
```

## Usage

```php
use TerminalDetector\TerminalDetector;
use function TerminalDetector\detectTerminal;

// Using the class
$result = TerminalDetector::detect();

// Or using the helper function
$result = detectTerminal();

$result->detected;       // true / false
$result->name;           // e.g. 'iterm2', 'ghostty', 'vscode'
$result->version;        // e.g. '3.5.0' (from TERM_PROGRAM_VERSION)
$result->knownTerminal(); // KnownTerminal enum or null
```

## Supported Terminals

| Terminal | Detection Method |
|---|---|
| iTerm2 | `TERM_PROGRAM` |
| Apple Terminal | `TERM_PROGRAM` |
| VS Code | `TERM_PROGRAM` |
| Hyper | `TERM_PROGRAM` |
| Warp | `TERM_PROGRAM` |
| WezTerm | `TERM_PROGRAM` |
| Rio | `TERM_PROGRAM` |
| Kitty | `KITTY_WINDOW_ID` / `TERM` |
| Ghostty | `GHOSTTY_RESOURCES_DIR` / `TERM` |
| Windows Terminal | `WT_SESSION` |
| JetBrains | `TERMINAL_EMULATOR` |
| Konsole | `KONSOLE_DBUS_SESSION` / `KONSOLE_VERSION` |
| Tilix | `TILIX_ID` |
| GNOME Terminal | `GNOME_TERMINAL_SCREEN` / `VTE_VERSION` |
| Tabby | `TABBY_CONFIG_DIRECTORY` |
| Alacritty | `TERM` |
| Xterm | `TERM` |
| tmux | `TMUX` |
| Zellij | `ZELLIJ` |
| Screen | `STY` |
| SSH | `SSH_CONNECTION` / `SSH_CLIENT` |

## Custom Override

Set the `TERMINAL_DETECTOR` environment variable to force a specific terminal name:

```bash
TERMINAL_DETECTOR=my-terminal php script.php
```

## License

Terminal Detector is open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.
