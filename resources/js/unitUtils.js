export const transformUnits = (units) => {
  let result = [];

  units.forEach(unit => {
    let transformedUnit = {
      key: unit.id,
      name: unit.name
    };

    if (typeof unit.parent_id === 'number') {
      transformedUnit.parent = unit.parent_id;
    }

    result.push(transformedUnit);

    if (unit.all_children) {
      result = result.concat(transformUnits(unit.all_children));
    }
  });

  return result;
};