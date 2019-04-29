/*
 * 
 * This file is part of Akounting
 * Copyright (C) 2007-2018  Soles 
 */

var provincia_list = [
  
   {value: 'Guatemala'},
   {value: 'Petén'},
   {value: 'Huehuetenango'},
   {value: 'Quiché'},
   {value: 'Alta Verapaz'},
   {value: 'Izabal'},
   {value: 'San Marcos'},
   {value: 'Quetzaltenango'},
   {value: 'Totonicapán'},
   {value: 'Sololá'},
   {value: 'Chimaltenango'},
   {value: 'Sacatepéquez'},
   {value: 'Baja Verapaz'},
   {value: 'El Progreso'},
   {value: 'Jalapa'},
   {value: 'Zacapa'},
   {value: 'Chiquimula'},
   {value: 'Retalhuleu'},
   {value: 'Suchitepéquez'},
   {value: 'Escuintla'},
   {value: 'Santa Rosa'},
   {value: 'Jutiapa'},
   
];

$(document).ready(function() {
   $("#ac_provincia, #ac_provincia2").autocomplete({
      lookup: provincia_list,
   });
});
