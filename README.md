## General
AzusaBanItem is a Pocketmine plug-in that works to block the use of items / blocks in the world

## Features
- Blocking blocks to be put into the world
- Blocking blocks for interaction in the world
- Block items for entity/player hits
- Block an item from being used, e.g. bow
  
## Configuration
```yaml
# AzusaBanItem Configuration

banned_items:
  - "stone"
  - "diamond sword"
  - "bow"
  - "chest"
  - "bread"

allworld: false

ban-mode: "whitelist" # Mode can be either "whitelist" or "blacklist"

world:
  - world
  - lobby

ban_message: "§cThis item/block is banned from the world"
```
