#!/bin/bash

DEPLOY_DIR="/var/www/multiregion-demo"

# ── IMDSv2 token ──────────────────────────────────────────────────────────────
TOKEN=$(curl -s -X PUT "http://169.254.169.254/latest/api/token" \
  -H "X-aws-ec2-metadata-token-ttl-seconds: 21600")

# ── Instance metadata ─────────────────────────────────────────────────────────
# Note: instance IP is read at request time by PHP via $_SERVER['SERVER_ADDR']
# Only the region is needed here for Parameter Store lookups and page display

AWS_REGION=$(curl -s -H "X-aws-ec2-metadata-token: $TOKEN" \
  http://169.254.169.254/latest/meta-data/placement/region)

# Region is critical — abort if we can't determine it
if [ -z "$AWS_REGION" ]; then
  echo "ERROR: Could not determine AWS region from instance metadata" >&2
  exit 1
fi

# ── Parameter Store ───────────────────────────────────────────────────────────
# Non-fatal — fall back to empty string if parameter is missing
DB_HOST=$(aws ssm get-parameter \
  --name "/multiregion-demo/db-host" \
  --region "$AWS_REGION" \
  --query "Parameter.Value" \
  --output text 2>/dev/null) || DB_HOST=""

OS_URL=$(aws ssm get-parameter \
  --name "/multiregion-demo/os-url" \
  --region "$AWS_REGION" \
  --query "Parameter.Value" \
  --output text 2>/dev/null) || OS_URL=""

# ── Random background colour ──────────────────────────────────────────────────
BG_COLOR=$(printf '#%06X\n' $((RANDOM * RANDOM % 16777215)))

# ── Replace placeholders ──────────────────────────────────────────────────────
sed -i "s|%%AWS_REGION%%|${AWS_REGION}|g"    "$DEPLOY_DIR/index.php"
sed -i "s|%%BG_COLOR%%|${BG_COLOR}|g"        "$DEPLOY_DIR/index.php"
sed -i "s|%%AWS_REGION%%|${AWS_REGION}|g"    "$DEPLOY_DIR/uploads-check.php"
sed -i "s|const DB_HOST = '';|const DB_HOST = '${DB_HOST}';|g" "$DEPLOY_DIR/config.php"
sed -i "s|const OS_URL = '';|const OS_URL = '${OS_URL}';|g"    "$DEPLOY_DIR/config.php"

# ── Seed uploads directory (EFS is shared — only copy if file absent) ─────────
SAMPLE="$DEPLOY_DIR/assets/sample.svg"
if [ -f "$SAMPLE" ] && [ ! -f "$DEPLOY_DIR/uploads/sample.svg" ]; then
  cp "$SAMPLE" "$DEPLOY_DIR/uploads/sample.svg"
  chown www-data:www-data "$DEPLOY_DIR/uploads/sample.svg"
fi
