import React from 'react';
import { Box } from '@mui/material';

interface AvatarProps extends Omit<React.ImgHTMLAttributes<HTMLImageElement>, 'src'> {
    /** The string used to generate a unique avatar. Can be a name, email, or ID. */
    seed: string;
    /** The size of the avatar in pixels. */
    size?: number;
    /** Toggles the glassmorphism effect on the container. */
    glassEffect?: boolean;
}

// You can easily change the style here to any other from dicebear.com
const AVATAR_STYLE = 'micah';

export const Avatar: React.FC<AvatarProps> = ({ seed, size = 80, glassEffect = false, className, ...props }) => {
    // Request a slightly larger image for better quality on high-res screens
    const avatarUrl = `https://api.dicebear.com/8.x/${AVATAR_STYLE}/png?seed=${encodeURIComponent(seed)}&size=${size * 1.5}`;

    const image = (
        <img
            src={avatarUrl}
            alt={`Avatar for ${seed}`}
            width={size}
            height={size}
            style={{ borderRadius: '50%', display: 'block' }}
            {...props}
        />
    );

    return glassEffect ? <Box className={`hero-img-container ${className ?? ''}`}>{image}</Box> : image;
};