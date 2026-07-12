# COCOMO Calculation for Hind Bihar

This document details the Constructive Cost Model (COCOMO) calculation for the Hind Bihar local news portal project. The estimation is based on the source code lines developed for the application, taking into account the MVC architecture provided by the CodeIgniter framework.

## Source Code Statistics

Based on the analysis of the `app`, `public`, and `tests` directories (excluding vendor and framework core files), the total number of Lines of Code (LOC) is approximately:

- **Total Lines of Code (LOC):** 19,510
- **Kilo Lines of Code (KLOC):** 19.51

## Estimation Model

For this project, we are using the **Basic COCOMO Model** in the **Organic Mode**, as this is a relatively small to medium-sized project developed by a familiar team with relaxed requirements.

The constants for the Organic Mode are:
- **a** = 2.4
- **b** = 1.05
- **c** = 2.5
- **d** = 0.38

### Formulas

- **Effort (E):** `a * (KLOC)^b` (Person-Months)
- **Development Time (D):** `c * (E)^d` (Months)
- **Average Staffing (S):** `E / D` (Persons)

## Calculations

**1. Effort Calculation:**
```text
E = 2.4 * (19.51)^1.05
E = 2.4 * 22.86
E ≈ 54.86 Person-Months
```
This means it would take approximately 55 person-months of effort to develop this project from scratch.

**2. Development Time Calculation:**
```text
D = 2.5 * (54.86)^0.38
D = 2.5 * 4.54
D ≈ 11.35 Months
```
The estimated time required to complete the project is about 11.35 months.

**3. Average Staffing Calculation:**
```text
S = 54.86 / 11.35
S ≈ 4.83 Persons
```
An optimal team size for this project would be around 5 developers.

## Summary

| Metric | Estimated Value |
| :--- | :--- |
| **Effort** | ~54.86 Person-Months |
| **Development Time** | ~11.35 Months |
| **Average Team Size** | ~5 Developers |

> **Note:** These calculations estimate the effort and time required to build the application logic, views, testing suites, and configurations (19,510 lines of PHP code) on top of the CodeIgniter framework.
