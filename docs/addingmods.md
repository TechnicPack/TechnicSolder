Adding Mods to the Mod Library
==============================

1. Go to the FTP or folder that you are hosting the repo in
2. Create a new folder called "mods" (no quotes) inside the repo folder
3. Create a new folder with the mod name ex. Damage indicators will become "damage-indicators" as a folder. (This is the mod slug, it can be anything, and its always in lowercase.)
4. Go to the Directory that you have your mod in that would like to use
5. Mod packages are unzipped to the root modpack directory. As such, they have to be packaged accordingly.
6. Upload the renamed file to the mod directory ex. the damage-indicators file we made in the last step will be uploaded to "whateveryourrepois/damage-indicators/"
7. Go to the Solder API GUI and go to "add mods" Type the name of the mod like normal so the name is Damage-indicators in this example.
8. Check that the "modslug" is the same as the folder we created earlier and then click add mod.
9. Click on the version tab
10. Go under the version column and type the version number so ex. damage-indicators-1.2.4.5.zip in the version column becomes 1.2.4.5
11. Click verify on the versions this will create an MD5 and then from here the mod is added

# Mod Package Structure examples

[See here for more information on the mod package structure](Zip file structure)

Example 1:
- modA-1.0.zip
  - mods/
    - modA-1.0.jar

Example 2:
- configPack-1.0.zip
  - config/
    - configA.cfg
    - configB.cfg
- config.txt
- server.dat