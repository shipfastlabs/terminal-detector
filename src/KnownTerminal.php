<?php

declare(strict_types=1);

namespace TerminalDetector;

enum KnownTerminal: string
{
    case ITerm2 = 'iterm2';
    case AppleTerminal = 'apple-terminal';
    case VSCode = 'vscode';
    case Hyper = 'hyper';
    case WarpTerminal = 'warp';
    case WezTerm = 'wezterm';
    case Kitty = 'kitty';
    case Alacritty = 'alacritty';
    case Ghostty = 'ghostty';
    case WindowsTerminal = 'windows-terminal';
    case JetBrains = 'jetbrains';
    case Konsole = 'konsole';
    case GnomeTerminal = 'gnome-terminal';
    case Tilix = 'tilix';
    case Tabby = 'tabby';
    case Rio = 'rio';
    case Tmux = 'tmux';
    case Zellij = 'zellij';
    case Screen = 'screen';
    case SSHSession = 'ssh';
    case Xterm = 'xterm';
}
