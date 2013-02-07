<?php

class Create_Mods {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up( ) {
		Schema::create( 'mods', function( $table ) {
			$table->increments( 'id' );

			$table->string( 'name', 160 );
			$table->string( 'description', 255 )->nullable( );
			$table->string( 'installtype', 10 )->nullable( );
			$table->string( 'modtype', 30 )->nullable( );
			$table->string( 'link', 255 )->nullable( );

			$table->timestamps( );
		} );
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down( ) {
		Schema::drop( 'mods' );
	}

}