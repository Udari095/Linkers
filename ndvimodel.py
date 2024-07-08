import rasterio
import numpy as np
import matplotlib.pyplot as plt
from matplotlib.colors import ListedColormap, BoundaryNorm

# Load the red (Band 4) and NIR (Band 8) bands
with rasterio.open('Areas/Baththaramulla/Band_4.tif') as red_src:
    red = red_src.read(1).astype('float64')

with rasterio.open('Areas/Baththaramulla/Band_8.tif') as nir_src:
    nir = nir_src.read(1).astype('float64')

# Calculate NDVI
ndvi = (nir - red) / (nir + red)

# Define classification thresholds
ndvi_class = np.zeros_like(ndvi)
ndvi_class[ndvi < 0] = 0                       # No vegetation
ndvi_class[(ndvi >= 0) & (ndvi < 0.2)] = 1     # Sparse vegetation
ndvi_class[(ndvi >= 0.2) & (ndvi < 0.5)] = 2   # Low vegetation
ndvi_class[(ndvi >= 0.5) & (ndvi < 0.7)] = 3   # Moderate vegetation
ndvi_class[ndvi >= 0.7] = 4                    # High vegetation

# Define the colors for the classification
colors = [
    [236 / 255, 4 / 255, 6 / 255],  # No vegetation
    [255 / 255, 154 / 255, 0 / 255],  # Sparse vegetation
    [255 / 255, 254 / 255, 11 / 255],  # Low vegetation
    [214 / 255, 255 / 255, 0 / 255],  # Moderate vegetation
    [109 / 255, 254 / 255, 0 / 255]  # High vegetation
]

# Create a colormap and normalize
cmap = ListedColormap(colors)
bounds = [-0.1, 0, 0.2, 0.5, 0.7, 1]
norm = BoundaryNorm(bounds, cmap.N)

# Plot NDVI
plt.figure(figsize=(7, 7))


plt.title("NDVI")
plt.imshow(ndvi, cmap=cmap, norm=norm)
plt.colorbar()


plt.show()