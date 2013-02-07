<?php

class Create_Builds {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'builds', function( $table ) {
			$table->increments( 'id' );

			$table->integer( 'modpack_id' )->unsigned( );
			$table->foreign( 'modpack_id' )->references( 'id' )->on( 'modpacks' );

			$table->string( 'version', 15 );

			$table->timestamps( );
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop( 'builds' );
	}

}