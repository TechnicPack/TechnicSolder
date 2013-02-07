<?php

class Create_Builddetails {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'builddetails', function( $table ) {
			$table->increments( 'id' );

			$table->integer( 'build_id' )->unsigned( );
			$table->foreign( 'build_id' )->references( 'id' )->on( 'builds' );

			$table->integer( 'modfile_id' )->unsigned( );
			$table->foreign( 'modfile_id' )->references( 'id' )->on( 'modfiles' );

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
		Schema::drop( 'builddetails' );
	}

}