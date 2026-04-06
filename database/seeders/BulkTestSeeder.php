<?php

namespace Database\Seeders;

use App\Models\Build;
use App\Models\Client;
use App\Models\Key;
use App\Models\Mod;
use App\Models\Modpack;
use App\Models\Modversion;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BulkTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding bulk test data...');

        // --- Users (30) ---
        $ip = '127.0.0.1';
        $admin = User::first();
        if (! $admin) {
            $admin = new User;
            $admin->username = 'admin';
            $admin->email = 'admin@example.com';
            $admin->password = 'admin';
            $admin->created_ip = $ip;
            $admin->last_ip = $ip;
            $admin->save();

            UserPermission::create([
                'user_id' => $admin->id,
                'solder_full' => true,
            ]);
        }

        $names = ['alice', 'bob', 'carol', 'dave', 'eve', 'frank', 'grace', 'heidi',
            'ivan', 'judy', 'karl', 'liam', 'mia', 'noah', 'olivia', 'pete',
            'quinn', 'rosa', 'sam', 'tina', 'uma', 'vince', 'wendy', 'xander',
            'yara', 'zack', 'amber', 'blake', 'chloe'];

        foreach ($names as $name) {
            if (User::where('username', $name)->exists()) {
                continue;
            }
            $u = new User;
            $u->username = $name;
            $u->email = "{$name}@example.com";
            $u->password = 'password';
            $u->created_ip = $ip;
            $u->last_ip = $ip;
            $u->updated_by_user_id = $admin->id;
            $u->updated_by_ip = $ip;
            $u->save();

            UserPermission::create([
                'user_id' => $u->id,
                'solder_full' => false,
                'modpacks_manage' => true,
            ]);
        }
        $this->command->info('  Users: '.User::count());

        // --- Clients (40) ---
        $clientNames = [
            'Alpha Launcher', 'Beta Client', 'Gamma Player', 'Delta Launcher',
            'Epsilon Client', 'Zeta Player', 'Eta Launcher', 'Theta Client',
            'Iota Player', 'Kappa Launcher', 'Lambda Client', 'Mu Player',
            'Nu Launcher', 'Xi Client', 'Omicron Player', 'Pi Launcher',
            'Rho Client', 'Sigma Player', 'Tau Launcher', 'Upsilon Client',
            'Phi Player', 'Chi Launcher', 'Psi Client', 'Omega Player',
            'Nebula Launcher', 'Pulsar Client', 'Quasar Player', 'Nova Launcher',
            'Comet Client', 'Meteor Player', 'Star Launcher', 'Moon Client',
            'Sun Player', 'Mars Launcher', 'Venus Client', 'Saturn Player',
            'Jupiter Launcher', 'Neptune Client', 'Pluto Player', 'Mercury Launcher',
        ];
        foreach ($clientNames as $name) {
            if (Client::where('name', $name)->exists()) {
                continue;
            }
            Client::create([
                'name' => $name,
                'uuid' => Str::uuid()->toString(),
            ]);
        }
        $this->command->info('  Clients: '.Client::count());

        // --- Keys (35) ---
        $keyNames = [
            'Production API', 'Staging API', 'Development API', 'CI/CD Pipeline',
            'Launcher v1', 'Launcher v2', 'Launcher Beta', 'Launcher Alpha',
            'Monitor Service', 'Analytics', 'CDN Edge 1', 'CDN Edge 2',
            'CDN Edge 3', 'Backup Service', 'Test Runner', 'Load Balancer',
            'Mirror US-East', 'Mirror US-West', 'Mirror EU', 'Mirror Asia',
            'Partner Acme', 'Partner Globex', 'Partner Initech', 'Partner Umbrella',
            'Internal Tool A', 'Internal Tool B', 'Mobile App iOS', 'Mobile App Android',
            'Web Dashboard', 'Discord Bot', 'Webhook Handler', 'Status Page',
            'Rate Limiter', 'Auth Gateway', 'Legacy Bridge',
        ];
        foreach ($keyNames as $name) {
            if (Key::where('name', $name)->exists()) {
                continue;
            }
            Key::create([
                'name' => $name,
                'api_key' => Str::random(64),
            ]);
        }
        $this->command->info('  Keys: '.Key::count());

        // --- Mods (60) with versions ---
        $modDefs = [
            ['Industrial Craft 2', 'industrialcraft-2', 'IC2 Dev Team', 'Energy and machines'],
            ['BuildCraft', 'buildcraft', 'SpaceToad', 'Pipes and automation'],
            ['Thermal Expansion', 'thermal-expansion', 'TeamCoFH', 'RF-powered tech'],
            ['Applied Energistics 2', 'applied-energistics-2', 'AlgorithmX2', 'Storage networks'],
            ['Tinkers Construct', 'tinkers-construct', 'mDiyo', 'Custom tools and weapons'],
            ['Ender IO', 'ender-io', 'CrazyPants', 'Compact machines and conduits'],
            ['Botania', 'botania', 'Vazkii', 'Nature magic'],
            ['Thaumcraft', 'thaumcraft', 'Azanor', 'Arcane magic and research'],
            ['Draconic Evolution', 'draconic-evolution', 'brandon3055', 'End-game power'],
            ['Mekanism', 'mekanism', 'aidancbrady', 'High-tech machinery'],
            ['Forestry', 'forestry', 'SirSengir', 'Bees and trees'],
            ['Railcraft', 'railcraft', 'CovertJaguar', 'Rails and carts'],
            ['Immersive Engineering', 'immersive-engineering', 'BluSunrize', 'Retro-tech'],
            ['Actually Additions', 'actually-additions', 'Ellpeck', 'Useful gadgets'],
            ['Extra Utilities 2', 'extra-utilities-2', 'RWTema', 'Random useful things'],
            ['Chisel', 'chisel', 'tterrag', 'Decorative blocks'],
            ['JourneyMap', 'journeymap', 'techbrew', 'Minimap and waypoints'],
            ['Just Enough Items', 'jei', 'mezz', 'Recipe viewer'],
            ['Iron Chests', 'iron-chests', 'progwml6', 'Better chests'],
            ['Waila', 'waila', 'ProfMobius', 'Block information HUD'],
            ['NEI', 'not-enough-items', 'chicken_bones', 'Item and recipe viewer'],
            ['Avaritia', 'avaritia', 'SpitefulFox', 'Endgame crafting'],
            ['Blood Magic', 'blood-magic', 'WayofTime', 'Blood-powered magic'],
            ['Witchery', 'witchery', 'Emoniph', 'Witchcraft and brews'],
            ['Pams HarvestCraft', 'pams-harvestcraft', 'MatrexsVigil', 'Food and farming'],
            ['Biomes O Plenty', 'biomes-o-plenty', 'Glitchfiend', 'New biomes'],
            ['Twilight Forest', 'twilight-forest', 'Benimatic', 'Adventure dimension'],
            ['Galacticraft', 'galacticraft', 'micdoodle8', 'Space exploration'],
            ['ComputerCraft', 'computercraft', 'dan200', 'In-game computers'],
            ['OpenComputers', 'opencomputers', 'Sangar', 'Modular computers'],
            ['Refined Storage', 'refined-storage', 'raoulvdberge', 'Storage system'],
            ['Storage Drawers', 'storage-drawers', 'jaquadro', 'Compact storage'],
            ['ProjectE', 'projecte', 'sinkillerj', 'Equivalent Exchange'],
            ['Mystcraft', 'mystcraft', 'XCompWiz', 'Custom dimensions'],
            ['GregTech', 'gregtech', 'GregoriusT', 'Hardcore tech'],
            ['EnderStorage', 'ender-storage', 'chicken_bones', 'Linked storage'],
            ['Wireless Redstone', 'wireless-redstone', 'chicken_bones', 'Remote signals'],
            ['MineFactory Reloaded', 'minefactory-reloaded', 'skyboy026', 'Automation'],
            ['Natura', 'natura', 'mDiyo', 'New trees and berries'],
            ['Jabba', 'jabba', 'ProfMobius', 'Better barrels'],
            ['BiblioCraft', 'bibliocraft', 'Nuchaz', 'Decorative storage'],
            ['Carpenter Blocks', 'carpenter-blocks', 'Mineshopper', 'Custom slopes'],
            ['Morpheus', 'morpheus', 'Quetzi', 'Multiplayer sleep voting'],
            ['Inventory Tweaks', 'inventory-tweaks', 'Kobata', 'Auto sort inventory'],
            ['OptiFine', 'optifine', 'sp614x', 'Performance and shaders'],
            ['Fastcraft', 'fastcraft', 'Player', 'Performance tweaks'],
            ['CodeChickenCore', 'codechickencore', 'chicken_bones', 'Core library'],
            ['CoFH Core', 'cofh-core', 'TeamCoFH', 'Core library for CoFH'],
            ['Mantle', 'mantle', 'mDiyo', 'Shared code for Tinkers'],
            ['Baubles', 'baubles', 'Azanor', 'Accessory slots'],
            ['Forge Multipart', 'forge-multipart', 'chicken_bones', 'Microblocks'],
            ['WAILA Harvestability', 'waila-harvestability', 'squeek502', 'Harvest info'],
            ['Damage Indicators', 'damage-indicators', 'rich1051414', 'Mob health'],
            ['Hats', 'hats', 'iChun', 'Wearable hats'],
            ['Morph', 'morph', 'iChun', 'Shape-shifting'],
            ['Tropicraft', 'tropicraft', 'Cojomax99', 'Tropical dimension'],
            ['Lucky Block', 'lucky-block', 'PlayerInDistress', 'Random drops'],
            ['Big Reactors', 'big-reactors', 'ErogenousBeef', 'Large power generation'],
            ['Extreme Reactors', 'extreme-reactors', 'ZeroNoRyouki', 'Updated Big Reactors'],
            ['Flux Networks', 'flux-networks', 'sonar_sonic', 'Wireless energy'],
        ];

        $mcVersions = ['1.7.10', '1.12.2', '1.16.5', '1.18.2', '1.19.4', '1.20.1'];

        foreach ($modDefs as [$prettyName, $slug, $author, $desc]) {
            if (Mod::where('name', $slug)->exists()) {
                continue;
            }
            $mod = Mod::create([
                'pretty_name' => $prettyName,
                'name' => $slug,
                'author' => $author,
                'description' => $desc,
                'link' => "https://curseforge.com/minecraft/mc-mods/{$slug}",
            ]);

            // 3-8 versions per mod
            $numVersions = rand(3, 8);
            for ($v = 1; $v <= $numVersions; $v++) {
                $mc = $mcVersions[array_rand($mcVersions)];
                Modversion::create([
                    'mod_id' => $mod->id,
                    'version' => "{$mc}-{$v}.0.".rand(0, 9),
                    'md5' => md5("{$slug}-{$v}-".rand()),
                    'filesize' => rand(10000, 5000000),
                ]);
            }

        }
        $this->command->info('  Mods: '.Mod::count().' ('.Modversion::count().' versions)');

        // --- Modpacks (30) with builds ---
        $packDefs = [
            ['Tekkit Classic', 'tekkit-classic', false, false],
            ['Tekkit Legends', 'tekkit-legends', false, false],
            ['Hexxit', 'hexxit', false, false],
            ['Attack of the B-Team', 'attack-of-the-b-team', false, false],
            ['Voltz', 'voltz', false, false],
            ['Big Dig', 'big-dig', true, false],
            ['Blightfall', 'blightfall', false, true],
            ['Crafters Paradise', 'crafters-paradise', false, false],
            ['Dragon Realm', 'dragon-realm', false, false],
            ['Elemental Craft', 'elemental-craft', true, false],
            ['Factory World', 'factory-world', false, false],
            ['Galaxy Explorers', 'galaxy-explorers', false, true],
            ['Hardcore Survival', 'hardcore-survival', false, false],
            ['Ice Age Pack', 'ice-age-pack', true, true],
            ['Jurassic Craft', 'jurassic-craft', false, false],
            ['Kingdom Builder', 'kingdom-builder', false, false],
            ['Lost Civilizations', 'lost-civilizations', false, false],
            ['Magic Academy', 'magic-academy', false, false],
            ['Nether Depths', 'nether-depths', true, false],
            ['Ocean Adventures', 'ocean-adventures', false, false],
            ['Pixelmon Reforged', 'pixelmon-reforged', false, false],
            ['Quest for Glory', 'quest-for-glory', false, true],
            ['Redstone Engineers', 'redstone-engineers', false, false],
            ['SkyFactory 4', 'skyfactory-4', false, false],
            ['Tech Horizons', 'tech-horizons', true, false],
            ['Underground Empire', 'underground-empire', false, false],
            ['Vanilla Plus', 'vanilla-plus', false, false],
            ['Wizard Academy', 'wizard-academy', false, false],
            ['Xtreme Survival', 'xtreme-survival', false, false],
            ['Zen Garden', 'zen-garden', true, true],
        ];

        foreach ($packDefs as [$name, $slug, $hidden, $private]) {
            if (Modpack::where('slug', $slug)->exists()) {
                continue;
            }
            $pack = Modpack::create([
                'name' => $name,
                'slug' => $slug,
                'hidden' => $hidden,
                'private' => $private,
                'icon' => false,
                'icon_md5' => null,
                'icon_url' => URL::asset('/resources/default/icon.png'),
                'logo' => false,
                'logo_md5' => null,
                'logo_url' => URL::asset('/resources/default/logo.png'),
                'background' => false,
                'background_md5' => null,
                'background_url' => URL::asset('/resources/default/background.jpg'),
            ]);

            // 3-12 builds per pack
            $numBuilds = rand(3, 12);
            $mc = $mcVersions[array_rand($mcVersions)];
            $recVersion = null;
            $latestVersion = null;
            for ($b = 1; $b <= $numBuilds; $b++) {
                $version = "{$b}.0.".rand(0, 9);
                $build = Build::create([
                    'modpack_id' => $pack->id,
                    'version' => $version,
                    'minecraft' => $mc,
                    'min_java' => '1.8',
                    'min_memory' => '2048',
                    'is_published' => $b <= $numBuilds - 1,
                    'private' => rand(0, 5) === 0,
                ]);

                // Attach 5-15 random mod versions to this build
                $randomVersions = Modversion::inRandomOrder()->limit(rand(5, 15))->pluck('id');
                $build->modversions()->syncWithoutDetaching($randomVersions);

                if ($b === max(1, $numBuilds - 1)) {
                    $recVersion = $version;
                }
                $latestVersion = $version;
            }

            if ($recVersion) {
                $pack->update(['recommended' => $recVersion, 'latest' => $latestVersion]);
            }
        }
        $this->command->info('  Modpacks: '.Modpack::count().' ('.Build::count().' builds)');

        // Backfill mod versions for builds that have none
        $emptyBuilds = Build::whereDoesntHave('modversions')->get();
        foreach ($emptyBuilds as $build) {
            $randomVersions = Modversion::inRandomOrder()->limit(rand(5, 15))->pluck('id');
            $build->modversions()->syncWithoutDetaching($randomVersions);
        }
        $this->command->info('  Backfilled mod versions for '.$emptyBuilds->count().' builds');

        $this->command->info('Done!');
    }
}
