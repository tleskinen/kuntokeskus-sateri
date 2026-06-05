#!/usr/bin/env bash
# Generoi kuva Atlas Cloud API:n kautta ja tallenna plugin-kansioon.
#
# Käyttö:
#   ./tools/generate-image.sh <out-file> <prompt> [model] [aspect_ratio]
#
# Esimerkit:
#   ./tools/generate-image.sh hero-img.png "Klimt-style gym scene"
#   ./tools/generate-image.sh hero.png "..." black-forest-labs/flux-2-pro/text-to-image 16:9
#
# Tarvitsee ATLAS_CLOUD_API_KEY:n .env.image.local:ssa.

set -euo pipefail

# Default-arvot
OUT_NAME="${1:-hero-img.png}"
PROMPT="${2:-A photorealistic gym interior with dramatic lighting}"
MODEL="${3:-black-forest-labs/flux-2-pro/text-to-image}"
ASPECT="${4:-16:9}"

# Polut
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
ENV_FILE="$ROOT_DIR/.env.image.local"
TARGET_DIR="$ROOT_DIR/saterinportti-ostopolku/assets/brand/Images/Boona"
TARGET="$TARGET_DIR/$OUT_NAME"

# Lataa avain
if [ ! -f "$ENV_FILE" ]; then
  echo "ERROR: $ENV_FILE not found" >&2
  exit 1
fi
set -a; source "$ENV_FILE"; set +a

if [ -z "${ATLAS_CLOUD_API_KEY:-}" ]; then
  echo "ERROR: ATLAS_CLOUD_API_KEY not set in $ENV_FILE" >&2
  exit 1
fi

mkdir -p "$TARGET_DIR"

echo "→ Sending request to Atlas Cloud..."
echo "  model:  $MODEL"
echo "  aspect: $ASPECT"
echo "  prompt: ${PROMPT:0:80}..."

# Tee POST-kutsu
REQ_BODY=$(python3 -c "
import json, sys
print(json.dumps({
    'model': sys.argv[1],
    'prompt': sys.argv[2],
    'aspect_ratio': sys.argv[3]
}))" "$MODEL" "$PROMPT" "$ASPECT")

RESP=$(curl -sS -X POST https://api.atlascloud.ai/api/v1/model/generateImage \
  -H "Authorization: Bearer $ATLAS_CLOUD_API_KEY" \
  -H "Content-Type: application/json" \
  -d "$REQ_BODY")

ID=$(echo "$RESP" | python3 -c "import json, sys; print(json.load(sys.stdin)['data']['id'])")
if [ -z "$ID" ]; then
  echo "ERROR: no prediction id in response:" >&2
  echo "$RESP" >&2
  exit 1
fi
echo "→ Prediction id: $ID"

# Poll until completed
echo -n "→ Waiting"
for i in $(seq 1 60); do
  sleep 3
  echo -n "."
  POLL=$(curl -sS "https://api.atlascloud.ai/api/v1/model/prediction/$ID" \
    -H "Authorization: Bearer $ATLAS_CLOUD_API_KEY")
  STATUS=$(echo "$POLL" | python3 -c "import json, sys; d=json.load(sys.stdin); print(d['data']['status'])" 2>/dev/null || echo "unknown")
  if [ "$STATUS" = "completed" ] || [ "$STATUS" = "succeeded" ]; then
    URL=$(echo "$POLL" | python3 -c "import json, sys; d=json.load(sys.stdin); print(d['data']['outputs'][0])")
    echo
    echo "→ Image URL: $URL"
    echo "→ Downloading to $TARGET"
    curl -sS -L "$URL" -o "$TARGET"
    ls -la "$TARGET"
    echo "✓ Done"
    exit 0
  fi
  if [ "$STATUS" = "failed" ] || [ "$STATUS" = "error" ]; then
    echo
    echo "ERROR: generation failed:" >&2
    echo "$POLL" | python3 -m json.tool >&2
    exit 1
  fi
done

echo
echo "ERROR: timed out after 3 minutes" >&2
exit 1
