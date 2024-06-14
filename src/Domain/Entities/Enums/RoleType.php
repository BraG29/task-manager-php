<?php

namespace App\Domain\Entities\Enums;

/**
 * Tipo de Roles y posibles asignaciones
 * <ul>
 *     <li>
 *         <code>ADMIN:</code> Quien crea el Proytecto o la Tarea.<br>
 *         Se puede asignar entre vinculo de Usuario con Proyecto y Tarea<br>
 *         <b>El usuario que crea la Tarea es tambien responsable de ella de manera irreversible</b>
 *     </li>
 *     <li>
 *         <code>EDITOR:</code> Quien tiene permisos para crear tareas en un proyecto.<br>
 *         Se puede asignar entre vinculo de Usuario con Proyecto<br>
 *     </li>
 *     <li>
 *         <code>READER:</code> Quien solo tiene permisos de lectura en un proyecto.<br>
 *         Se puede asignar entre vinculo de Usuario con Proyecto<br>
 *     </li>
 *     <li>
 *         <code>RESPONSIBLE:</code> Quien es responsable de una tarea.<br>
 *         Se puede asignar entre vinculo de Usuario con Tarea<br>
 *     </li>
 * </ul>
 */
enum RoleType: int
{
    case ADMIN = 0;
    case EDITOR = 1;
    case READER = 2;
    case RESPONSIBLE = 3;
}
