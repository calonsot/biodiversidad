CREATE OR REPLACE FUNCTION update_changetimestamp_column()
RETURNS TRIGGER AS $$
BEGIN
   NEW.fecha_actualizacion = now(); 
   RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_ab_changetimestamp BEFORE UPDATE
    ON tags FOR EACH ROW EXECUTE PROCEDURE 
    update_changetimestamp_column();