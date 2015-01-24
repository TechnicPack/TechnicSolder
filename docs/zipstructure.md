Zip File Structure
==================

The zipped mods must at least include a "mods" folder, within which you place the .jar or .zip original mod file. For example, if you are including the bibliocraft mod, you must place the bibliocraft.jar (the name of the file is irrelevant) inside a "mods" directory.

Then you must zip the mods directory and name it according to the TechnicSolder naming convention ([modname]-[version].zip), and place it within your mods repository within a directory named [modname].

If you want to include customized configuration files for your mod pack, you should create a "config" directory at the same level as the "mods" directory mentioned above, and place your customised files within it. You then include both directories in your zip file. The structure might look like this:

- mods/
  - modfile.jar
- config/
  - modconfig.cfg
  - modconfig_directory/
    - otherconfig.cfg
    - onemoreconfig.cfg

This is the structure you should see if you unzip your file.

One important exception is that you MUST include the forge loader mod in your mod pack (otherwise it won't load any mods!). This is not done using a "mods" directory, but a "bin" directory, and the name of your jar file MUST be "modpack.jar". Use the same naming convention as above to zip this directory and add it to your mod pack just like any other mod.
