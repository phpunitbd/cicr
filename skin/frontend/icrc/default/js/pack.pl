#!/usr/bin/perl

use strict;
use warnings;

use JavaScript::Packer;

my $packer = JavaScript::Packer->init();

open( UNCOMPRESSED, $ARGV[0] ) or die "Cannot open $ARGV[0]";
open( COMPRESSED, ">$ARGV[1]" ) or die "Cannot open $ARGV[1]";

my $uncompressed = join( '', <UNCOMPRESSED> );

my $compressed = $packer->minify( \$uncompressed, { compress => 'best' } );

print COMPRESSED $compressed;
close(UNCOMPRESSED);
close(COMPRESSED);

