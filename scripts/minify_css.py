#!/usr/bin/env python3
"""
Regenerate assets/css/main.min.css from assets/css/main.css.

Usage:
    python3 scripts/minify_css.py
"""
from __future__ import annotations

import re
from pathlib import Path


def minify(css: str) -> str:
    """Return a minimal CSS string (very small helper, no external deps)."""
    no_comments = re.sub(r"/\*.*?\*/", "", css, flags=re.S)
    collapsed_ws = re.sub(r"\s+", " ", no_comments)
    tightened = re.sub(r"\s*([{}:;,>])\s*", r"\1", collapsed_ws)
    tightened = tightened.replace(";}", "}")
    return tightened.strip()


def main() -> None:
    repo_root = Path(__file__).resolve().parents[1]
    src = repo_root / "assets" / "css" / "main.css"
    dst = repo_root / "assets" / "css" / "main.min.css"

    css = src.read_text(encoding="utf-8")
    dst.write_text(minify(css) + "\n", encoding="utf-8")
    print(f"Minified {src.relative_to(repo_root)} → {dst.relative_to(repo_root)}")


if __name__ == "__main__":
    main()
