## General
AzusaBanItem is a Pocketmine plug-in that works to block the use of items / blocks in the world

## Features
- Block banned blocks from being placed by players
- Block banned blocks for interaction by players.
- Block banned items/tools from dealing damage to entities or players
- Block items/tools that are banned for use by players
- Block banned foods to increase hunger bar
- OP player is not affected, can be set in config

## Command
Commands | Default | Permission
--- | --- | ---
`/banitem` | Op | azusabanitem.command.banitem
`/unbanbanitem` | Op | azusabanitem.command.unbanitem
`/banitemlist` | Op | azusabanitem.command.banitemlist

## Configuration
```yaml
# AzusaBanItem Configuration

# Allows op players to not be affected by banned items
allow-op: true

# Message when successfully ban and unban items
ban_success_message: "§aItem has been successfully banned"
unban_success_message: "§aItem has been successfully unbanned"

# Message when an item has been previously banned
already_banned_message: "§cThe item is already on the banned"

# Message when the item you want to ban is not in the player's hand
no_item_message: "§cNo blocks/items detected in your hand"

# Message when unbanned item but item is not banned
not_banned_message: "§cThe item is not banned"

# Message when a banned item is used by a player
banned_usage_message: "§cThis item/block is banned from the world"
```

## Does not support items
- [🗙] Totem of undying
- [🗙] Spyglass
