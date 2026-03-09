#!/bin/bash

echo "Stopping containers and wiping the database volume..."
sudo docker compose down -v

echo "Clearing server logs..."
> logs/server.log

echo "Removing uploaded profile images..."
find public/uploads/profile_images/ -type f -not -name '.gitkeep' -delete

echo "Environment completely reset!"