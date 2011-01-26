BEGIN;
insert into supplychain (user_id, created, modified) values (1, extract(epoch from now()), extract(epoch from now()));
--select currval('supplychain_id_seq') into new_supplychain_id;
\set new_supplychain_id currval('supplychain_id_seq')
insert into stop (supplychain_id, local_stop_id, geometry) values 
    (:new_supplychain_id, 1, ST_SetSRID(ST_GeometryFromText('POINT(-10804007.180522 3869332.593955)'), 3857)),
    (:new_supplychain_id, 2, ST_SetSRID(ST_GeometryFromText('POINT(-7929147.678904 5239202.289146)'), 3857)),
    (:new_supplychain_id, 3, ST_SetSRID(ST_GeometryFromText('POINT(-12489606.041822 3954200.282625)'), 3857)),
    (:new_supplychain_id, 4, ST_SetSRID(ST_GeometryFromText('POINT(-10634992.255936 3485526.892738)'), 3857)),
    (:new_supplychain_id, 5, ST_SetSRID(ST_GeometryFromText('POINT(-9349165.430522 4044184.943345)'), 3857));
insert into stop_attribute (supplychain_id, local_stop_id, "key", "value") values
    (:new_supplychain_id, 1, 'name', 'Facility #1'),
    (:new_supplychain_id, 2, 'name', 'Facility #2'),
    (:new_supplychain_id, 3, 'name', 'Facility #3'),
    (:new_supplychain_id, 4, 'name', 'Facility #4'),
    (:new_supplychain_id, 5, 'name', 'Facility #5');
insert into hop (supplychain_id, from_stop_id, to_stop_id, geometry) values
    (:new_supplychain_id, 3, 1, ST_SetSRID(ST_GeometryFromText('MULTILINESTRING((-12489606.041822 3954200.282625, -10804007.180522 3869332.593955))'), 3857)), 
    (:new_supplychain_id, 3, 2, ST_SetSRID(ST_GeometryFromText('MULTILINESTRING((-12489606.041822 3954200.282625, -7929147.678904 5239202.289146))'), 3857)),
    (:new_supplychain_id, 3, 4, ST_SetSRID(ST_GeometryFromText('MULTILINESTRING((-12489606.041822 3954200.282625, -10634992.255936 3485526.892738))'), 3857)), 
    (:new_supplychain_id, 3, 5, ST_SetSRID(ST_GeometryFromText('MULTILINESTRING((-12489606.041822 3954200.282625, -9349165.430522 4044184.943345))'), 3857));

END;
