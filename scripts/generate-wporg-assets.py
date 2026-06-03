#!/usr/bin/env python3
"""Generate simple WordPress.org-ready placeholder assets for SNWorks Cron Diagnostics.

Uses only Python stdlib so it works in minimal CI/server environments.
"""

from __future__ import annotations

import math
import struct
import zlib
from pathlib import Path
from typing import Iterable, Tuple

Color = Tuple[int, int, int, int]

OUT = Path(__file__).resolve().parents[1] / ".wordpress-org"


def blend(a: Color, b: Color, t: float) -> Color:
    return tuple(int(a[i] + (b[i] - a[i]) * t) for i in range(4))  # type: ignore[return-value]


def write_png(path: Path, width: int, height: int, pixels: Iterable[Color]) -> None:
    raw = bytearray()
    it = iter(pixels)
    for _y in range(height):
        raw.append(0)
        for _x in range(width):
            raw.extend(next(it))

    def chunk(kind: bytes, data: bytes) -> bytes:
        return struct.pack(">I", len(data)) + kind + data + struct.pack(">I", zlib.crc32(kind + data) & 0xFFFFFFFF)

    png = b"\x89PNG\r\n\x1a\n"
    png += chunk(b"IHDR", struct.pack(">IIBBBBB", width, height, 8, 6, 0, 0, 0))
    png += chunk(b"IDAT", zlib.compress(bytes(raw), 9))
    png += chunk(b"IEND", b"")
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_bytes(png)


def circle_mask(x: float, y: float, cx: float, cy: float, r: float) -> bool:
    return (x - cx) ** 2 + (y - cy) ** 2 <= r ** 2


def line_distance(px: float, py: float, ax: float, ay: float, bx: float, by: float) -> float:
    dx = bx - ax
    dy = by - ay
    if dx == dy == 0:
        return math.hypot(px - ax, py - ay)
    t = max(0.0, min(1.0, ((px - ax) * dx + (py - ay) * dy) / (dx * dx + dy * dy)))
    return math.hypot(px - (ax + t * dx), py - (ay + t * dy))


def banner(width: int, height: int) -> list[Color]:
    bg1: Color = (20, 24, 58, 255)
    bg2: Color = (77, 44, 150, 255)
    accent: Color = (69, 220, 180, 255)
    white: Color = (245, 248, 255, 255)
    soft: Color = (124, 102, 220, 255)
    pixels: list[Color] = []
    cx = int(width * 0.24)
    cy = height // 2
    radius = int(min(width, height) * 0.28)
    ring = max(6, int(radius * 0.08))

    for y in range(height):
        for x in range(width):
            t = (x / max(1, width - 1) + y / max(1, height - 1)) / 2
            color = blend(bg1, bg2, t)

            # subtle diagonal dashboard lines
            if (x + y * 2) % max(24, width // 24) < 2:
                color = blend(color, soft, 0.18)

            d = math.hypot(x - cx, y - cy)
            if radius - ring <= d <= radius + ring:
                color = blend(color, accent, 0.9)
            if circle_mask(x, y, cx, cy, radius * 0.14):
                color = white

            # clock hands
            if line_distance(x, y, cx, cy, cx, cy - radius * 0.55) < max(3, ring / 2):
                color = white
            if line_distance(x, y, cx, cy, cx + radius * 0.42, cy + radius * 0.25) < max(3, ring / 2):
                color = white

            # check mark / safe signal
            ax, ay = width * 0.64, height * 0.55
            if line_distance(x, y, ax, ay, ax + width * 0.05, ay + height * 0.09) < max(5, height * 0.018):
                color = accent
            if line_distance(x, y, ax + width * 0.05, ay + height * 0.09, ax + width * 0.17, ay - height * 0.13) < max(5, height * 0.018):
                color = accent

            pixels.append(color)
    return pixels


def icon(size: int) -> list[Color]:
    bg1: Color = (22, 28, 72, 255)
    bg2: Color = (79, 45, 154, 255)
    accent: Color = (69, 220, 180, 255)
    white: Color = (245, 248, 255, 255)
    pixels: list[Color] = []
    cx = cy = size / 2
    radius = size * 0.34
    ring = max(4, size * 0.035)

    for y in range(size):
        for x in range(size):
            t = (x + y) / (2 * max(1, size - 1))
            color = blend(bg1, bg2, t)
            d = math.hypot(x - cx, y - cy)
            if radius - ring <= d <= radius + ring:
                color = accent
            if circle_mask(x, y, cx, cy, size * 0.045):
                color = white
            if line_distance(x, y, cx, cy, cx, cy - radius * 0.55) < max(2, ring / 2):
                color = white
            if line_distance(x, y, cx, cy, cx + radius * 0.45, cy + radius * 0.25) < max(2, ring / 2):
                color = white
            if line_distance(x, y, size * 0.32, size * 0.70, size * 0.44, size * 0.82) < max(3, size * 0.03):
                color = accent
            if line_distance(x, y, size * 0.44, size * 0.82, size * 0.72, size * 0.58) < max(3, size * 0.03):
                color = accent
            pixels.append(color)
    return pixels


def main() -> None:
    write_png(OUT / "banner-1544x500.png", 1544, 500, banner(1544, 500))
    write_png(OUT / "banner-772x250.png", 772, 250, banner(772, 250))
    write_png(OUT / "icon-256x256.png", 256, 256, icon(256))
    write_png(OUT / "icon-128x128.png", 128, 128, icon(128))
    for path in sorted(OUT.glob("*.png")):
        print(path)


if __name__ == "__main__":
    main()
