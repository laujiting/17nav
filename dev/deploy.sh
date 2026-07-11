#!/bin/bash
# 17Nav 部署脚本
# 将主题和插件部署到 ECS 的 Typecho 实例
# 用法: bash dev/deploy.sh

set -e

SSH_KEY="$HOME/.ssh/id_rsa"
ECS_USER="skyline"
ECS_HOST="118.196.80.220"
TYPECHO_DATA="/var/typecho"

echo "🚀 17Nav 部署开始..."

# 1. 部署主题
echo "📦 部署主题..."
scp -r -i "$SSH_KEY" theme/* "$ECS_USER@$ECS_HOST:/tmp/17nav-theme/"
ssh -i "$SSH_KEY" "$ECS_USER@$ECS_HOST" "sudo cp -r /tmp/17nav-theme/* $TYPECHO_DATA/themes/17nav/ && sudo chown -R 33:33 $TYPECHO_DATA/themes/17nav/"

# 2. 部署插件
echo "🔌 部署插件..."
scp -r -i "$SSH_KEY" plugin/Nav17Manager/* "$ECS_USER@$ECS_HOST:/tmp/17nav-plugin/"
ssh -i "$SSH_KEY" "$ECS_USER@$ECS_HOST" "sudo cp -r /tmp/17nav-plugin/* $TYPECHO_DATA/plugins/Nav17Manager/ && sudo chown -R 33:33 $TYPECHO_DATA/plugins/Nav17Manager/"

echo "✅ 部署完成！"
echo "访问: https://nav.17ai.icu/"
