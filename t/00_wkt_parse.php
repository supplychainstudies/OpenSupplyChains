<?php

sm_test('wkt_read_multilinestring', true, is_array(Sourcemap_Wkt::read('MULTILINESTRING((3 4,10 50,20 25),(-5 -8,-10 -8,-15 -4))')));
