<h3>Today</h3>
<p>
    <?php if($today->users): ?><?= $today->users ?> new user<?= $today->users > 1 ? 's' : '' ?>.
    <?php else: ?>No new users.
    <?php endif; ?>
    <?php if($today->maps): ?><?= $today->maps ?> new map<?= $today->maps > 1 ? 's' : '' ?>.
    <?php else: ?>No new maps.
    <?php endif; ?>
    <?php if($today->logins): ?><?= $today->logins ?> user<?= $today->logins > 1 ? 's' : '' ?> logged in.
    <?php else: ?>No logins.
    <?php endif; ?>
</p>

<?php $chbaseurl = "http://chart.googleapis.com/chart?";  ?>
<?php $chsz = "220x200"; ?>
<h3>The Last Week</h3>
<p>
    <?php if($lastweek->users): ?><?= $lastweek->users ?> new user<?= $lastweek->users > 1 ? 's' : '' ?>.
    <?php else: ?>No new users.
    <?php endif; ?>
    <?php if($lastweek->maps): ?><?= $lastweek->maps ?> new map<?= $lastweek->maps > 1 ? 's' : '' ?>.
    <?php else: ?>No new maps.
    <?php endif; ?>
    <?php if($lastweek->logins): ?><?= $lastweek->logins ?> user<?= $lastweek->logins > 1 ? 's' : '' ?> logged in.
    <?php else: ?>No logins.
    <?php endif; ?>
</p>

<img src="<?= $chbaseurl.'chf=bg,s,ffffff&chco=00BEFA&cht=bvs&chd=t:'.join(',',$week_maps).'&chds=a&chxt=x,y&chxl=0:|7|6|5|4|3|2|1|0|&chxr=0,7,0|1,0,'.max($week_maps).'&chtt=New+Maps&chs='.$chsz ?>" />
<img src="<?= $chbaseurl.'chf=bg,s,ffffff&chco=FA00C0&cht=bvs&chd=t:'.join(',',$week_users).'&chds=a&chxt=x,y&chxl=0:|7|6|5|4|3|2|1|0|&chxr=0,7,0|1,0,'.max($week_users).'&chtt=New+Users&chs='.$chsz ?>" />
<img src="<?= $chbaseurl.'chf=bg,s,ffffff&chco=FAB700&cht=bvs&chd=t:'.join(',',$week_logins).'&chds=a&chxt=x,y&chxl=0:|7|6|5|4|3|2|1|0|&chxr=0,7,0|1,0,'.max($week_logins).'&chtt=User+Logins&chs='.$chsz ?>" />


<h3>The Last Month</h3>
<p>
    <?php if($thismonth->users): ?><?= $thismonth->users ?> new user<?= $thismonth->users > 1 ? 's' : '' ?>.
    <?php else: ?>No new users.
    <?php endif; ?>
    <?php if($thismonth->maps): ?><?= $thismonth->maps ?> new map<?= $thismonth->maps > 1 ? 's' : '' ?>.
    <?php else: ?>No new maps.
    <?php endif; ?>
    <?php if($thismonth->logins): ?><?= $thismonth->logins ?> user<?= $thismonth->logins > 1 ? 's' : '' ?> logged in.
    <?php else: ?>No logins.
    <?php endif; ?>
</p>
<img src="<?= $chbaseurl.'chf=bg,s,ffffff&chco=00BEFA&cht=bvs&chd=t:'.join(',',$fourweeks_maps).'&chds=a&chxt=x,y&chxl=0:|4|3|2|1|0|&chxr=0,4,0|1,0,'.max($fourweeks_maps).'&chtt=New+Maps&chs='.$chsz ?>" />
<img src="<?= $chbaseurl.'chf=bg,s,ffffff&chco=FA00C0&cht=bvs&chd=t:'.join(',',$fourweeks_users).'&chds=a&chxt=x,y&chxl=0:|4|3|2|1|0|&chxr=0,4,0|1,0,'.max($fourweeks_users).'&chtt=New+Users&chs='.$chsz ?>" />
<img src="<?= $chbaseurl.'chf=bg,s,ffffff&chco=FAB700&cht=bvs&chd=t:'.join(',',$fourweeks_logins).'&chds=a&chxt=x,y&chxl=0:|4|3|2|1|0|&chxr=0,4,0|1,0,'.max($fourweeks_logins).'&chtt=User+Logins&chs='.$chsz ?>" />


<h3>The Last Six Months</h3>
<p>
    <?php if($sixmos->users): ?><?= $sixmos->users ?> new user<?= $sixmos->users > 1 ? 's' : '' ?>.
    <?php else: ?>No new users.
    <?php endif; ?>
    <?php if($sixmos->maps): ?><?= $sixmos->maps ?> new map<?= $sixmos->maps > 1 ? 's' : '' ?>.
    <?php else: ?>No new maps.
    <?php endif; ?>
    <?php if($sixmos->logins): ?><?= $sixmos->logins ?> user<?= $sixmos->logins > 1 ? 's' : '' ?> logged in.
    <?php else: ?>No logins.
    <?php endif; ?>
</p>

<img src="<?= $chbaseurl.'chf=bg,s,ffffff&chco=00BEFA&cht=bvs&chd=t:'.join(',',$sixmos_maps).'&chds=a&chxt=x,y&chxl=0:|6|5|4|3|2|1|0|&chxr=0,6,0|1,0,'.max($sixmos_maps).'&chtt=New+Maps&chs='.$chsz ?>" />
<img src="<?= $chbaseurl.'chf=bg,s,ffffff&chco=FA00C0&cht=bvs&chd=t:'.join(',',$sixmos_users).'&chds=a&chxt=x,y&chxl=0:|6|5|4|3|2|1|0|&chxr=0,6,0|1,0,'.max($sixmos_users).'&chtt=New+Users&chs='.$chsz ?>" />
<img src="<?= $chbaseurl.'chf=bg,s,ffffff&chco=FAB700&cht=bvs&chd=t:'.join(',',$sixmos_logins).'&chds=a&chxt=x,y&chxl=0:|6|5|4|3|2|1|0|&chxr=0,6,0|1,0,'.max($sixmos_logins).'&chtt=User+Logins&chs='.$chsz ?>" />
