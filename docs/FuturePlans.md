# Future Plans

In here i will talk about potential future features to implement into this ORM.

### Ideas

- ❌ Enums (ex. gender enum for either 'male' or 'female').

---

- ✅ strict types -> models will need to define a structure with properties.
- ❌ update method
- ❌ get method
- ✅ find static method (::where('id',1)->first() shorthand)
- ❌ relationships (hasMany, belongsTo, hasOne)

---

- ❌ $readonly model property - ensures model is read only (no writes)
- ❌ $primary_key model property - specify the primary key name
- ❌ $hidden model property - properties that are hidden from printing functions etc.
