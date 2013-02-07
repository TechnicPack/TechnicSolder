<?php

class Create_Modpacks {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'modpacks', function( $table ) {
			$table->increments( 'id' );

			$table->string( 'name', 160 );
			$table->string( 'description', 255 )->nullable( );
			$table->string( 'icon_md5', 32 );
			$table->string( 'logo_md5', 32 );
			$table->string( 'background_md5', 32 );

			$table->integer( 'recommended' )->unsigned( );
			$table->foreign( 'recommended' )->references( 'id' )->on( 'builds' );

			$table->integer( 'latest' )->unsigned( );
			$table->foreign( 'latest' )->references( 'id' )->on( 'builds' );

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
		Schema::drop( 'modpacks' );
	}

}